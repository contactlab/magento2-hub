<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 24/01/18
 * Time: 10:52
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\ProductView;
use Contactlab\Hub\Helper\Data as HubHelper;

class ControllerActionPostdispatchCatalogProductView implements ObserverInterface
{
    protected $_customerSession;
    protected $_registry;
    protected $_eventService;
    protected $_strategy;
    protected $_helper;

    public function __construct(
        Session $customerSession,
        EventManagementInterface $eventService,
        Registry $registry,
        ProductView $strategy,
        HubHelper $helper
    )
    {
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $product =  $this->_registry->registry('product');
        if(!$this->_helper->isJsTrackingEnabled($product->getStoreId()))
        {
            $data['product'] = $this->_helper->getObjProduct($product);
            $data['store_id'] = $product->getStoreId();
            $data['email'] = $this->_customerSession->getCustomer()->getEmail();
            $this->_strategy->setContext($data);
            $this->_eventService->collectEvent($this->_strategy);
        }
    }
}