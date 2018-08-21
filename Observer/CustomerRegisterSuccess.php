<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 14/12/17
 * Time: 16:21
 */

namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\Register;


class CustomerRegisterSuccess implements ObserverInterface
{
    protected $_eventService;
    protected $_strategy;
    protected $_customerSession;

    public function __construct(
        EventManagementInterface $eventService,
        Register $strategy,
        Session $customerSession
    )
    {
        $this->_eventService = $eventService;
        $this->_strategy = $strategy;
        $this->_customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Customer\Model\Data\Customer $customer
         */
        $customer = $observer->getCustomer();
        $data['email'] = $customer->getEmail();
        $data['store_id'] = $customer->getStoreId();
        $this->_strategy->setContext($data);
        $this->_eventService->collectEvent($this->_strategy);
    }
}