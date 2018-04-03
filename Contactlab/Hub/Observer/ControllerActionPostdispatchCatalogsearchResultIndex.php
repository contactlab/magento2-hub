<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 24/01/18
 * Time: 15:13
 */


namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\Layer\Resolver as Layer;
use Magento\Store\Model\StoreManagerInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\Search;
use Contactlab\Hub\Helper\Data as HubHelper;

class ControllerActionPostdispatchCatalogsearchResultIndex implements ObserverInterface
{
    protected $_customerSession;
    protected $_layer;
    protected $_storeManager;
    protected $_eventService;
    protected $_strategy;
    protected $_helper;

    public function __construct(
        Session $customerSession,
        EventManagementInterface $eventService,
        Layer $layer,
        StoreManagerInterface $storeManager,
        Search $strategy,
        HubHelper $helper
    )
    {
        $this->_customerSession = $customerSession;
        $this->_layer = $layer;
        $this->_storeManager = $storeManager;
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        if(!$this->_helper->isJsTrackingEnabled($storeId))
        {
            $searchQuery = $observer->getRequest()->getParam('q');
            $currentLayer = $this->_layer->get();
            $searchResult = ($currentLayer) ? count($currentLayer->getProductCollection()->getAllIds()) : 0;

            $data['keyword'] = $searchQuery;
            $data['resultCount'] = $searchResult;
            $data['email'] = $this->_customerSession->getCustomer()->getEmail();
            $data['store_id'] = $storeId;
            $this->_strategy->setContext($data);
            $this->_eventService->collectEvent($this->_strategy);
        }
    }
}