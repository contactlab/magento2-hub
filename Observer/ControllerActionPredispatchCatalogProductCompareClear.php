<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 09/06/17
 * Time: 16:34
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CompareRemoveProduct;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory as CompareItemCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;

class ControllerActionPredispatchCatalogProductCompareClear implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_itemCollectionFactory;
    protected $_customerSession;
    protected $_customerVisitor;

    public function __construct(
        EventManagementInterface $eventService,
        CompareRemoveProduct $strategy,
        CompareItemCollectionFactory $itemCollectionFactory,
        Session $customerSession,
        Visitor $customerVisitor
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_customerVisitor = $customerVisitor;
    }

    public function execute(Observer $observer)
    {
        $items = $this->_itemCollectionFactory->create();
        if ($this->_customerSession->isLoggedIn()) {
            $items->setCustomerId($this->_customerSession->getCustomerId());
        } else {
            $items->setVisitorId($this->_customerVisitor->getId());
        }
        $items->useProductItem(true);
        foreach($items->getItems() as $item)
        {
            $item->setStoreId($item->getItemStoreId());
            $this->_strategy->setContext($item->getData());
            $this->_eventService->collectEvent($this->_strategy);
        }
    }
}