<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 13/06/17
 * Time: 14:23
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\OrderCanceled;
use Magento\Sales\Model\Order;

class SalesOrderSaveAfter implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_productRepository;

    public function __construct(
        EventManagementInterface $eventService,
        OrderCanceled $strategy
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
    }

    public function execute(Observer $observer)
    {
        $order =  $observer->getOrder();
        $OldStatus = $order->getOrigData('status');
        $NewStatus = $order->getStatus();
        if(($NewStatus == Order::STATE_CANCELED)
            && ($OldStatus != $NewStatus))
        {
            $this->_strategy->setContext($order->getData());
            $this->_eventService->collectEvent($this->_strategy);
        }
    }
}