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
use Magento\Customer\Model\Session;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\Login;


class CustomerLogin  implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_customerSession;

    public function __construct(
        EventManagementInterface $eventService,
        Login $strategy,
        Session $customerSession
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
        if(!$this->_eventService->isToRemoveSidCookie() && $this->_eventService->getSid())
        {
            $this->_strategy->setContext($customer->getData());
            $this->_eventService->collectEvent($this->_strategy);
        }
        else
        {
            $this->_customerSession->setHubLoginEvent($customer->getData());
        }
    }
}