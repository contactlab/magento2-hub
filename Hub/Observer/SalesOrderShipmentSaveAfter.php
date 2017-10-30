<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 13/06/17
 * Time: 15:46
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\OrderShipped;

class SalesOrderShipmentSaveAfter implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_productRepository;

    public function __construct(
        EventManagementInterface $eventService,
        OrderShipped $strategy
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
    }

    public function execute(Observer $observer)
    {
        $shipment =  $observer->getShipment();
        $order = $shipment->getOrder();
        $data = $shipment->getData();
        $data['remote_ip'] = $order->getRemoteIp();
        $data['customer_email'] = $order->getCustomerEmail();
        $this->_strategy->setContext($data);
        $this->_eventService->collectEvent($this->_strategy);
    }
}