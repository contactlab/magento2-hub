<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Contactlab\Hub\Helper\Data as Helper;


class LayoutGenerateBlocksAfter  implements ObserverInterface
{

    protected $_helper;

    public function __construct(
        Helper $helper
    )
    {
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        if($this->_helper->isJsTrackingEnabled($this->_helper->getStore()->getStoreId()))
        {
            $fullActionName = $observer->getFullActionName();
            if (empty($fullActionName)) {
                return;
            }
            $block = $observer->getLayout()->getBlock('contactlab_hub_js');
            if ($block) {
                $block->setFullActionName($fullActionName);
            }
        }
    }
}