<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 12:02
 */

namespace Contactlab\Hub\Model\Service;

use Contactlab\Hub\Api\AbandonedCartManagementInterface;
use Contactlab\Hub\Model\AbandonedCartFactory;
use Contactlab\Hub\Api\Data\AbandonedCartInterface;
use Contactlab\Hub\Api\AbandonedCartRepositoryInterface;
use Contactlab\Hub\Model\Event\Strategy\AbandonedCart as AbandonedCartStrategy;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Helper\Data as HubHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\ResourceConnection;

class AbandonedCart implements AbandonedCartManagementInterface
{
    protected $_abandonedCartFactory;
    protected $_abandonedCartRepository;
    protected $_storeInterface;
    protected $_quoteRepository;
    protected $_strategy;
    protected $_eventService;
    protected $_helper;
    protected $_searchCriteriaBuilder;
    protected $_sortOrder;
    protected $_quoteCollection;
    protected $_resource;

    public function __construct(
        AbandonedCartFactory $abandonedCartFactory,
        AbandonedCartRepositoryInterface $abandonedCartRepository,
        StoreManagerInterface $storeInterface,
        CartRepositoryInterface $quoteRepository,
        AbandonedCartStrategy $strategy,
        EventManagementInterface $eventService,
        HubHelper $helper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrder $sortOrder,
        QuoteCollectionFactory $quoteCollection,
        ResourceConnection $resource
    ){
        $this->_abandonedCartFactory = $abandonedCartFactory;
        $this->_abandonedCartRepository = $abandonedCartRepository;
        $this->_storeInterface = $storeInterface;
        $this->_quoteRepository = $quoteRepository;
        $this->_strategy = $strategy;
        $this->_eventService = $eventService;
        $this->_helper = $helper;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sortOrder = $sortOrder;
        $this->_quoteCollection = $quoteCollection;
        $this->_resource =  $resource;
    }

    /**
     * Collect Abandoned Carts
     *
     * @return AbandonedCartManagementInterface
     */
    public function collectAbandonedCarts()
    {
        foreach($this->_storeInterface->getStores() as $storeId => $store)
        {
            if($this->_helper->isEnableEvent(AbandonedCartStrategy::HUB_EVENT_NAME, $storeId))
            {
                $this->_getAbandonedCartsFromQuote($storeId);
            }
        }
        $this->_createEventsFromAbandonedCarts();
    }

    protected function _getAbandonedCartsFromQuote($storeId)
    {
        $minMinutes = $this->_helper->getMinMinutesBeforeSendAbandonedCart($storeId);
        $minMinutesFromLastUpdate = new \Zend_Date();
        $minMinutesFromLastUpdate->subMinute($minMinutes);
        $maxMinutes = $this->_helper->getMaxMinutesBeforeSendAbandonedCart($storeId);
        $maxMinutesFromLastUpdate = new \Zend_Date();
        $maxMinutesFromLastUpdate->subMinute($maxMinutes);
        $collectionFactory = $this->_quoteCollection->create();
        $collectionFactory->addFieldToSelect(array('store_id','customer_email','created_at','updated_at','remote_ip'));
        $collectionFactory->addFieldToFilter('main_table.reserved_order_id', array('null' => true))
            ->addFieldToFilter('main_table.customer_email', array('notnull' => true))
            ->addFieldToFilter('main_table.items_count', array('gt' => 0))
            ->addFieldToFilter('main_table.store_id', array('eq' => $storeId));
        if($minMinutes)
        {
            $collectionFactory->getSelect()->where("(main_table.updated_at + INTERVAL ".$minMinutes." MINUTE) < ? ", date('Y-m-d H:i:s'));
        }
        if($maxMinutes)
        {
            $collectionFactory->addFieldToFilter('main_table.updated_at', array('gt' => $maxMinutesFromLastUpdate->get('YYYY-MM-dd HH:mm:ss')));
        }
        $subscribers = !$this->_helper->sendAbandonedCartToNotSubscribed();
        if($subscribers)
        {
            $collectionFactory->getSelect()->join(
                array('subscribers' => $this->_resource->getTableName('newsletter_subscriber')),
                    'subscribers.subscriber_email = main_table.customer_email', array()
            );
            $collectionFactory->addFieldToFilter('subscribers.subscriber_status', array('eq' => Subscriber::STATUS_SUBSCRIBED));
        }
        
        //echo $collectionFactory->getSelect();
        //echo "\n\ncollection\n\n";
        //die();

        foreach ($collectionFactory->getItems() as $cart)
        {
            $this->_searchCriteriaBuilder->addFilter(AbandonedCartInterface::QUOTE_ID,
                $cart->getEntityId());
            $this->_searchCriteriaBuilder->setCurrentPage(1); /** first page (means limit 0,1) */
            $this->_searchCriteriaBuilder->setPageSize(1); /** only get 1 product */
            $sortOrder = $this->_sortOrder->setField('updated_at')->setDirection(SortOrder::SORT_DESC);
            $this->_searchCriteriaBuilder->setSortOrders([$sortOrder]);

            $oldAbandonedCarts = $this->_abandonedCartRepository
                                    ->getList($this->_searchCriteriaBuilder->create())
                                    ->getItems();
            if(count($oldAbandonedCarts) == 1)
            {
                $oldAbandonedCart = $oldAbandonedCarts[0];
                if(strtotime($cart->getUpdatedAt()) > strtotime($oldAbandonedCart->getUpdatedAt()))
                {
                    $newAbandonedCart = $this->_abandonedCartFactory->create();
                    $newAbandonedCart->setQuoteId($cart->getEntityId());
                    $newAbandonedCart->setStoreId($cart->getStoreId());
                    $newAbandonedCart->setEmail($cart->getCustomerEmail());
                    $newAbandonedCart->setCreatedAt($cart->getCreatedAt());
                    $newAbandonedCart->setUpdatedAt($cart->getUpdatedAt());
                    $newAbandonedCart->setAbandonedAt($cart->getUpdatedAt());
                    $newAbandonedCart->setRemoteIp($cart->getRemoteIp());
                    $this->_abandonedCartRepository->save($newAbandonedCart);
                }
            }
        }
        return $this;
    }

    protected function _createEventsFromAbandonedCarts()
    {
        $this->_searchCriteriaBuilder->addFilter(AbandonedCartInterface::IS_EXPORTED, 0);
        $abandonedCarts = $this->_abandonedCartRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
        foreach ($abandonedCarts as $abandonedCart)
        {
            $data = $abandonedCart->getData();
            $this->_strategy->setContext($data);
            $this->_eventService->collectEvent($this->_strategy);

            $abandonedCart->setIsExported(1);
            $this->_abandonedCartRepository->save($abandonedCart);
        }
    }
}