<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CompareAddProduct;
use Contactlab\Hub\Helper\Data as HubHelper;

class CatalogProductCompareAddProduct implements ObserverInterface
{
    protected $_customerSession;
    protected $_eventService;
    protected $_strategy;
    protected $_helper;

    public function __construct(
        Session $customerSession,
        EventManagementInterface $eventService,
        CompareAddProduct $strategy,
        HubHelper $helper
    )
    {
        $this->_customerSession = $customerSession;
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $product =  $observer->getProduct();
        $data['product'] = $this->_helper->getObjProduct($product);
        $data['store_id'] = $product->getStoreId();
        $data['email'] = $this->_customerSession->getCustomer()->getEmail();
        $this->_strategy->setContext($data);
        $this->_eventService->collectEvent($this->_strategy);
    }
}