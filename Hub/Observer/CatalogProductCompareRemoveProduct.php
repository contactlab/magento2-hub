<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CompareRemoveProduct;

class CatalogProductCompareRemoveProduct implements ObserverInterface
{
    protected $_customerSession;
    protected $_eventService;
    protected $_strategy;

    public function __construct(
        Session $customerSession,
        EventManagementInterface $eventService,
        CompareRemoveProduct $strategy
    )
    {
        $this->_customerSession = $customerSession;
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
    }

    public function execute(Observer $observer)
    {
        $product =  $observer->getProduct();
        $data = $product->getData();
        $data['email'] = $this->_customerSession->getCustomer()->getEmail();
        $this->_strategy->setContext($data);
        $this->_eventService->collectEvent($this->_strategy);
    }
}