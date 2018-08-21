<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 13/06/17
 * Time: 09:01
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\StoreChange;
use Contactlab\Hub\Model\Event\Strategy\Login;
use Contactlab\Hub\Helper\Data as Helper;

class ControllerActionPredispatch implements ObserverInterface
{
    protected $_cookieManager;
    protected $_customerSession;
    protected $_storeManager;
    protected $_eventService;
    protected $_storeChangeStrategy;
    protected $_loginStrategy;
    protected $_helper;

    public function __construct(
        StoreCookieManagerInterface $cookieManager,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        EventManagementInterface $eventService,
        StoreChange $storeChangeStrategy,
        Login $loginStrategy,
        Helper $helper
    )
    {
        $this->_cookieManager = $cookieManager;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_eventService = $eventService;
        $this->_storeChangeStrategy = $storeChangeStrategy;
        $this->_loginStrategy = $loginStrategy;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $this->_checkChangeLanguage();
        $this->_checkLoginEvent();
    }
    
    protected function _checkChangeLanguage()
    {
        $storeCode = null;
        if ($this->_cookieManager->getStoreCodeFromCookie())
        {
            $storeCode = $this->_cookieManager->getStoreCodeFromCookie();
        }
        else
        {
            $storeCode = $this->_storeManager->getDefaultStoreView()->getCode();
        }

        if($sessionStoreCode = $this->_customerSession->getFromStoreCode())
        {
            if ($sessionStoreCode != $storeCode)
            {
                $oldStoreId = $this->_storeManager->getStore($sessionStoreCode)->getId();
                $newStoreId = $this->_storeManager->getStore($storeCode)->getId();
                $data = array();
                $data['email'] = $this->_customerSession->getCustomer()->getEmail();
                $data['store_id'] = $newStoreId;
                $data['setting'] = 'LANGUAGE';
                $data['old_value'] = $this->_helper->getStoreDefaultLocale($oldStoreId);
                $data['new_value'] = $this->_helper->getStoreDefaultLocale($newStoreId);
                $this->_storeChangeStrategy->setContext($data);
                $this->_eventService->collectEvent($this->_storeChangeStrategy);
            }
        }
        $this->_customerSession->setFromStoreCode($storeCode);
        return $this;
    }

    protected function _checkLoginEvent()
    {
        if($data = $this->_customerSession->getHubLoginEvent())
        {
            $this->_loginStrategy->setContext($data);
            $this->_eventService->collectEvent($this->_loginStrategy);
            $this->_customerSession->setHubLoginEvent();
        }
        return $this;
    }
    
}

