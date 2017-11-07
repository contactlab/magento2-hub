<?php
/*
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutGenerateBlocksAfter  implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
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
*/

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\Logout;

class CustomerLogout  implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_cookieManager;

    public function __construct(
        EventManagementInterface $eventService,
        Logout $strategy
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
    }

    public function execute(Observer $observer)
    {
        if($this->_eventService->getSid())
        {
            $customer = $observer->getCustomer();
            $this->_strategy->setContext($customer->getData());
            $this->_eventService->collectEvent($this->_strategy);
        }
    }
}