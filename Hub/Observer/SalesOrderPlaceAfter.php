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

class SalesOrderPlaceAfter implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_productRepository;

    public function __construct(
        EventManagementInterface $eventService,
        OrderCompleted $strategy
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
    }

    public function execute(Observer $observer)
    {
        $order =  $observer->getOrder();
        $this->_strategy->setContext($order->getData());
        $this->_eventService->collectEvent($this->_strategy);
    }
}