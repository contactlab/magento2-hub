<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class LayoutGenerateBlocksAfter  implements ObserverInterface
{

    public function execute(Observer $observer)
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