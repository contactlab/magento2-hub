<?php
namespace Contactlab\Hub\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Model\Event\Strategy\NewsletterSubscribed;
use Contactlab\Hub\Model\Event\Strategy\NewsletterUnsubscribed;
use Contactlab\Hub\Helper\Data as HubHelper;

class NewsletterSubscriberSaveBefore  implements ObserverInterface
{
    protected $_eventService;
    protected $_subscribeStrategy;
    protected $_unsubscribeStrategy;
    protected $_subscriber;
    protected $_helper;

    public function __construct(
        EventManagementInterface $eventService,
        NewsletterSubscribed $subscribeStrategy,
        NewsletterUnsubscribed $unsubscribeStrategy,
        SubscriberFactory $subscriberFactory,
        HubHelper $helper

    )
    {
        $this->_eventService = $eventService;
        $this->_subscribeStrategy = $subscribeStrategy;
        $this->_unsubscribeStrategy = $unsubscribeStrategy;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $subscriber = $observer->getDataObject();
        if($subscriber->getSubscriberStatus() == Subscriber::STATUS_SUBSCRIBED)
        {
            if(!$subscriber->getCreatedAt())
            {
                $subscriber->setCreatedAt(date('Y-m-d H:i:s'));
            }
            $subscriber->setLastSubscribedAt(date('Y-m-d H:i:s'));
        }
        elseif($subscriber->getSubscriberStatus() == Subscriber::STATUS_UNSUBSCRIBED)
        {
            $subscriber->setLastSubscribedAt();
        }

        if($this->_helper->isDiabledSendingSubscriptionEmail($subscriber->getStoreId())) {
            $subscriber->setImportMode(true);
        }

        if($this->_eventService->getSid())
        {
            $email = $observer->getDataObject()->getEmail();
            $newSubscriberStatus = $observer->getDataObject()->getSubscriberStatus();
            $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
            if ($subscriber->getId())
            {
                $oldSubscriberStatus = $subscriber->getSubscriberStatus();
            }
            else
            {
                $oldSubscriberStatus = Subscriber::STATUS_UNSUBSCRIBED;
            }
            if ($oldSubscriberStatus != $newSubscriberStatus)
            {
                $strategy = $this->_unsubscribeStrategy;
                if ($newSubscriberStatus == Subscriber::STATUS_SUBSCRIBED)
                {
                    $strategy = $this->_subscribeStrategy;
                }
                $strategy->setContext($observer->getDataObject()->getData());
                $this->_eventService->collectEvent($strategy);
            }
        }
    }
}