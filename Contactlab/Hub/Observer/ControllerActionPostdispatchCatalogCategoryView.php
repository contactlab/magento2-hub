<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 24/01/18
 * Time: 15:00
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\CategoryView;
use Contactlab\Hub\Helper\Data as HubHelper;

class ControllerActionPostdispatchCatalogCategoryView implements ObserverInterface
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
        CategoryView $strategy,
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
        $category =  $this->_registry->registry('current_category');
        if(!$this->_helper->isJsTrackingEnabled($category->getStoreId()))
        {
            $data = $category->getData();
            $data['email'] = $this->_customerSession->getCustomer()->getEmail();
            $this->_strategy->setContext($data);
            $this->_eventService->collectEvent($this->_strategy);
        }
    }
}