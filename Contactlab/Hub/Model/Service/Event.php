<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Service;

use Contactlab\Hub\Api\Data\EventInterface;
use Contactlab\Hub\Api\EventManagementInterface;
use Contactlab\Hub\Api\EventStrategyInterface;
use Contactlab\Hub\Api\EventRepositoryInterface;
use Contactlab\Hub\Api\HubManagementInterface;
use Contactlab\Hub\Model\EventFactory;
use Contactlab\Hub\Helper\Data as HubHelper;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
//use Magento\Framework\Api\DataObjectHelper;

class Event implements EventManagementInterface
{
    const COOKIE_SID_NAME = '_ch';
    const HUB_EVENT_SCOPE_ADMIN = 'admin';
    const HUB_EVENT_SCOPE_FRONTEND = 'frontend';

    protected $_eventFactory;
    protected $_eventRepository;
    protected $_cookieManager;
    protected $_hubService;
    protected $_helper;
    protected $_searchCriteriaBuilder;

    protected $_sessionId;

    public function __construct(
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        HubManagementInterface $hubService,
        HubHelper $helper,
        CookieManagerInterface $cookieManager,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ){
        $this->_eventFactory = $eventFactory;
        $this->_eventRepository = $eventRepository;
        $this->_hubService = $hubService;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Collect Event
     *
     * @param StrategyEventInterface $strategy
     * @param array $arguments
     * @return EventInterface $event
     */
    public function collectEvent(EventStrategyInterface $strategy, $arguments = [])
    {
        $data = $strategy->build();
        $event = $this->_eventFactory->create();
        if(!array_key_exists('created_at', $data))
        {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if(!array_key_exists('env_user_agent', $data))
        {
            $data['env_user_agent'] = $this->_helper->getUserAgent();
        }
        if(!array_key_exists('env_remote_ip', $data))
        {
            $data['env_remote_ip'] = $this->_helper->getRemoteIpAddress();
        }
        $data['event_data'] = json_encode($data['event_data']);
        if($data['scope'] == self::HUB_EVENT_SCOPE_FRONTEND)
        {
            $data['session_id'] = $this->getSid();
        }
        $event->setData($data);
        $this->_eventRepository->save($event);
        return $event;
    }

    public function isToRemoveSidCookie()
    {
        $return = false;
        $cookie = json_decode($this->_cookieManager->getCookie(self::COOKIE_SID_NAME));
        if (isset($cookie->customerId))
        {
            $this->_cookieManager->deleteCookie(self::COOKIE_SID_NAME);
            $return = true;
        }
        return $return;
    }

    public function getSid()
    {
        if(!$this->_sessionId) {
            $cookie = json_decode($this->_cookieManager->getCookie(self::COOKIE_SID_NAME));
            if ($cookie->sid) {
                $this->_sessionId = $cookie->sid;
            } else {
                $this->_helper->log('Cookie disabled');
            }
        }
        return $this->_sessionId;
    }

    /**
     * Send Event
     *
     * @param EventInterface $event
     * @return $this
     */
    public function sendEvent(EventInterface $event)
    {
        $this->_helper->log(__METHOD__);
        try
        {
            $hubCustomerId = null;
            $event->setStatus(EventInterface::EVENT_STATUS_RUNNING);
            $this->_hubService->setStoreId($event->getStoreId());
            $hubCustomerId = $this->_hubService->updateCustomer($event);
            $event->setHubCustomerId($hubCustomerId);
            $hubEvent = $this->_hubService->composeHubEvent($event);
            $event->setHubEvent(json_encode($hubEvent));
            $this->_hubService->postEvent($hubEvent);
            $event->setExportedAt(date('Y-m-d H:i:s'));
            $event->setStatus(EventInterface::EVENT_STATUS_EXPORTED);
            $this->_eventRepository->save($event);
        }

        catch (\RuntimeException $e)
        {
            $event->setStatus(EventInterface::EVENT_STATUS_RETRY);
            $this->_eventRepository->save($event);
            $this->_helper->log($e->getMessage());
        }
        catch (\Exception $e)
        {
            $event->setStatus(EventInterface::EVENT_STATUS_ERROR);
            $this->_eventRepository->save($event);
            $this->_helper->log($e->getMessage());
        }

        $this->_helper->log('fine export event');
        return $this;
    }


    protected function _getUnexportedEvents($pageSize = null)
    {
        $this->_searchCriteriaBuilder->addFilter(EventInterface::STATUS,
            array(EventInterface::EVENT_STATUS_UNEXPORTED, EventInterface::EVENT_STATUS_RETRY), 'in');
        if($pageSize)
        {
            $this->_searchCriteriaBuilder->setCurrentPage(1);
            $this->_searchCriteriaBuilder->setPageSize((int)$pageSize);
        }
        return $this->_eventRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
    }

    protected function _getEventsToClean($pageSize = null)
    {

        $months = $this->_helper->getMonthsToClean();
        $time = strtotime(date("Y-m-d"));
        $date = date("Y-m-d", strtotime("-".$months." month", $time));
        $this->_searchCriteriaBuilder
            ->addFilter(EventInterface::STATUS,
                array(EventInterface::EVENT_STATUS_EXPORTED, EventInterface::EVENT_STATUS_ERROR), 'in')
            ->addFilter(EventInterface::CREATED_AT, $date, 'lt');
        if($pageSize)
        {
            $this->_searchCriteriaBuilder->setCurrentPage(1);
            $this->_searchCriteriaBuilder->setPageSize((int)$pageSize);
        }
        return $this->_eventRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
    }

    /**
     * Export Events
     *
     * @param int $pageSize
     * @return $this
     */
    public function exportEvents($pageSize = 1)
    {
        foreach($this->_getUnexportedEvents($pageSize) as $event)
        {
            $this->sendEvent($event);
        }
    }

    /**
     * Clean Events
     *
     * @return $this
     */
    public function cleanEvents()
    {
        foreach($this->_getEventsToClean() as $event)
        {
            $event->delete();
        }
    }
}
