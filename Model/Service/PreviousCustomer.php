<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 14:57
 */

namespace Contactlab\Hub\Model\Service;

use Contactlab\Hub\Api\PreviousCustomerManagementInterface;
use Contactlab\Hub\Model\PreviousCustomerFactory;
use Contactlab\Hub\Api\Data\PreviousCustomerInterface;
use Contactlab\Hub\Api\PreviousCustomerRepositoryInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Helper\Data as HubHelper;
use Contactlab\Hub\Model\Event\Strategy\Login;
use Contactlab\Hub\Model\Event\Strategy\Register;
use Contactlab\Hub\Model\Event\Strategy\NewsletterSubscribed;
use Contactlab\Hub\Model\Event\Strategy\OrderCompleted;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Order;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubcriberCollectionFactory;

class PreviousCustomer  implements PreviousCustomerManagementInterface
{
    protected $_previousCustomerFactory;
    protected $_previousCustomerRepository;
    protected $_storeInterface;
    protected $_eventService;
    protected $_helper;
    protected $_searchCriteriaBuilder;
    protected $_filterGroupBuilder;
    protected $_sortOrder;
    protected $_resource;
    protected $_customerFactory;
    protected $_orderCollectionFactory;
    protected $_strategyLogin;
    protected $_strategyRegister;
    protected $_strategySubscriber;
    protected $_strategyOrderComplete;
    protected $_subcriberCollectionFactory;

    const CONTACTLAB_HUB_PREVIOUS_CUSTOMER_TABLE = 'contactlab_hub_previous_customer';

    public function __construct(
        PreviousCustomerFactory $previousCustomerFactory,
        PreviousCustomerRepositoryInterface $previousCustomerRepository,
        StoreManagerInterface $storeInterface,
        EventManagementInterface $eventService,
        HubHelper $helper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrder $sortOrder,     
        ResourceConnection $resource,
        CustomerCollectionFactory $customerFactory,
        OrderCollectionFactory $orderCollectionFactory,
        Login $strategyLogin,
        Register $strategyRegister,
        NewsletterSubscribed $strategySubscriber,
        OrderCompleted $strategyOrderComplete,
        SubcriberCollectionFactory $subcriberCollectionFactory
    )
    {
        $this->_previousCustomerFactory = $previousCustomerFactory;
        $this->_previousCustomerRepository = $previousCustomerRepository;
        $this->_storeInterface = $storeInterface;
        $this->_eventService = $eventService;
        $this->_helper = $helper;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterGroupBuilder = $filterGroupBuilder;
        $this->_sortOrder = $sortOrder;
        $this->_resource = $resource;
        $this->_customerFactory = $customerFactory;
        $this->_strategyLogin = $strategyLogin;
        $this->_strategyRegister = $strategyRegister;
        $this->_strategySubscriber = $strategySubscriber;
        $this->_strategyOrderComplete = $strategyOrderComplete;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_subcriberCollectionFactory = $subcriberCollectionFactory;
    }

    /**
     * Collect Previous Customers
     *
     * @param $pageSize
     * @return PreviousCustomerManagementInterface
     */
    public function collectPreviousCustomers($pageSize = 1)
    {
        foreach ($this->_storeInterface->getStores() as $storeId => $store) 
        {
            if ($this->_helper->isEnabledPreviousCustomer($storeId))
            {
                $fromDate = $this->_helper->getPreviousDate($storeId);
                $this->_getPreviousCustomersFromDate($fromDate, $storeId, $pageSize);
                $this->_getPreviousSubscribers($storeId, $pageSize);
                $this->_createEventsFromPreviousCustomers($fromDate, $storeId);
            }
        }
        return $this;
    }


    protected function _getPreviousCustomersFromDate($fromDate, $storeId, $pageSize = 1)
    {
        $collectionFactory = $this->_customerFactory->create();
        $collectionFactory
            ->addAttributeToFilter('store_id', array('in' => array(0, $storeId)))
            ->addAttributeToFilter('created_at', array('lteq' => $fromDate));
        $collectionFactory->getSelect()->joinLeft(
            array('previous_customer' => $this->_resource->getTableName( self::CONTACTLAB_HUB_PREVIOUS_CUSTOMER_TABLE)
            ), 'previous_customer.email = e.email', array()
        );
        $collectionFactory->getSelect()->where("previous_customer.customer_id IS NULL");
        $collectionFactory->getSelect()->limit($pageSize);
        //echo $collectionFactory->getSelect();
        foreach ($collectionFactory as $customer)
        {
            $remoteIp = $this->_helper->getRemoteIpAddress($customer->getEntityId());
            $previousCustomer = $this->_previousCustomerFactory->create();
            $previousCustomer->setCustomerId($customer->getEntityId())
                ->setStoreId($customer->getStoreId())
                ->setEmail($customer->getEmail())
                ->setCreatedAt($customer->getCreatedAt())
                ->setRemoteIp($remoteIp);
            $this->_previousCustomerRepository->save($previousCustomer);
        }

    }


    protected function _getPreviousSubscribers($storeId, $pageSize = 1)
    {
        $collectionFactory = $this->_subcriberCollectionFactory->create();
        $collectionFactory->useOnlySubscribed()
            ->addFieldToFilter('store_id', array('in' => array(0, $storeId)))
            ->addFieldToFilter('main_table.customer_id', array('eq' => 0));

        $collectionFactory->getSelect()->joinLeft(
            array('previous_customer' => $this->_resource->getTableName( self::CONTACTLAB_HUB_PREVIOUS_CUSTOMER_TABLE)
            ), 'previous_customer.email = main_table.subscriber_email', array()
        );

        $collectionFactory->getSelect()->where("previous_customer.customer_id IS NULL");
        $collectionFactory->getSelect()->limit($pageSize);
        //echo $collectionFactory->getSelect();
        foreach ($collectionFactory as $subscriber)
        {
            $previousCustomer = $this->_previousCustomerFactory->create();
            $previousCustomer->setEmail($subscriber->getEmail())
                ->setStoreId($subscriber->getStoreId())
                ->setCreatedAt(date('Y-m-d H:i:s'));
            $this->_previousCustomerRepository->save($previousCustomer);
        }
    }


    protected function _createEventsFromPreviousCustomers($fromDate, $storeId)
    {
        $this->_searchCriteriaBuilder
            ->addFilter(PreviousCustomerInterface::IS_EXPORTED, 0)
            ->addFilter(PreviousCustomerInterface::STORE_ID, array(0, $storeId), 'in');
        $previousCustomers = $this->_previousCustomerRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
        foreach ($previousCustomers as $previousCustomer)
        {
            $data = null;
            if($previousCustomer['customer_id'])
            {
                /*
                $this->_strategyLogin->setContext($previousCustomer->getData());
                $this->_eventService->collectEvent($this->_strategyLogin);
                */
                $data = $previousCustomer->getData();
                $data['scope'] = Event::HUB_EVENT_SCOPE_ADMIN;
                $this->_strategyRegister->setContext($data);
                $this->_eventService->collectEvent($this->_strategyRegister);

                if($this->_helper->canExportPreviousOrders($storeId))
                {
                    foreach ($this->_getCustomerOrders($previousCustomer->getCustomerId(), $fromDate) as $order)
                    {
                        $this->_strategyOrderComplete->setContext($order->getData());
                        $this->_eventService->collectEvent($this->_strategyOrderComplete);
                    }
                }
            }
            else
            {

                $data = $previousCustomer->getData();
                $data['subscriber_email'] = $data['email'];
                $data['scope'] = Event::HUB_EVENT_SCOPE_ADMIN;
                $this->_strategySubscriber->setContext($data);
                $this->_eventService->collectEvent($this->_strategySubscriber);
            }

            $previousCustomer->setIsExported(1);
            $this->_previousCustomerRepository->save($previousCustomer);
        }

        $this->_searchCriteriaBuilder
            ->addFilter(PreviousCustomerInterface::IS_EXPORTED, 0);
        $totPrevious = $this->_previousCustomerRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();


        if(count($totPrevious) == 0)
        {
            $this->_helper->setIsEnabledPreviousCustomer(0);
            $this->_helper->setExportPreviousOrders(0);
        }
    }

    protected function _getCustomerOrders($customerId, $fromDate)
    {
        $collectionFactory = $this->_orderCollectionFactory->create($customerId);
        $collectionFactory->addFieldToSelect('*')
            ->addFieldToFilter('created_at', array('lteq' => $fromDate))
            ->addFieldToFilter('state', array('neq' => Order::STATE_CANCELED))
            ->setOrder('created_at', 'desc');
        return $collectionFactory;
    }

    /**
     * Reset Previous Customer
     *
     * @return PreviousCustomerManagementInterface
     */
    public function resetPreviousCustomers()
    {
        foreach ($this->_storeInterface->getStores() as $storeId => $store)
        {
            $this->_helper->setPreviousDate(date('Y/m/d'), $storeId);
        }
        $this->_helper->setIsEnabledPreviousCustomer(1);
        $this->_setPreviousCustomerAsUnexported();
        return $this;
    }

    protected function _setPreviousCustomerAsUnexported()
    {
        $previousCustomers = $this->_previousCustomerRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
        foreach ($previousCustomers as $previousCustomer)
        {
            $previousCustomer->setIsExported(0);
            $this->_previousCustomerRepository->save($previousCustomer);
        }
    }
}