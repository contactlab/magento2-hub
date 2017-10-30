<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 16:51
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Contactlab\Hub\Helper\Data as HubHelper;

class AdminSystemConfigChangedSection implements ObserverInterface
{
    protected $_helper;
    protected $_storeInterface;

    public function __construct(
        HubHelper $helper,
        StoreManagerInterface $storeInterface
    )
    {
        $this->_helper = $helper;
        $this->_storeInterface = $storeInterface;
    }

    public function execute(Observer $observer)
    {
        foreach($this->_storeInterface->getStores() as $storeId => $store)
        {
            if(!$this->_helper->getPreviousDate($storeId))
            {
                $this->_helper->setPreviousDate(date('Y/m/d'), $storeId);
            }
        }
    }
}