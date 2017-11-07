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
use Contactlab\Hub\Model\Event\Strategy\OrderCompleted;
use Contactlab\Hub\Model\Event\Strategy\OrderCanceled;
use Magento\Sales\Model\Order;
use Contactlab\Hub\Helper\Data as HubHelper;

class SalesOrderSaveAfter implements ObserverInterface
{
    protected $_eventService;
    protected $_canceledStrategy;
    protected $_completedStrategy;
    protected $_productRepository;
    protected $_helper;

    public function __construct(
        EventManagementInterface $eventService,
        OrderCanceled $canceledStrategy,
        OrderCompleted $completedStrategy,
        HubHelper $helper
    )
    {
        $this->_eventService = $eventService;
        $this->_canceledStrategy = $canceledStrategy;
        $this->_completedStrategy = $completedStrategy;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        if (
            (in_array($order->getStatus(), $this->_helper->getOrderStatusToBeSent($order->getStoreId())))
            && (!$order->getContactlabHubExported())
        )
        {
            $this->_completedStrategy->setContext($order->getData());
            $this->_eventService->collectEvent($this->_completedStrategy);
            $order->setContactlabHubExported(true);
            $order->save();
        }
        $OldStatus = $order->getOrigData('status');
        $NewStatus = $order->getStatus();
        if(($NewStatus == Order::STATE_CANCELED)
            && ($OldStatus != $NewStatus))
        {
            $this->_canceledStrategy->setContext($order->getData());
            $this->_eventService->collectEvent($this->_canceledStrategy);
        }
    }
}