<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 23/06/17
 * Time: 16:39
 */

namespace Contactlab\Hub\Cron;

use Contactlab\Hub\Helper\Data as HubHelper;
use Contactlab\Hub\Api\EventManagementInterface;

class Event
{
    protected $_helper;
    protected $_eventService;

    public function __construct(
        HubHelper $helper,
        EventManagementInterface $eventService
    ) {
        $this->_helper = $helper;
        $this->_eventService = $eventService;
    }

    public function execute()
    {
        $this->_helper->log(__METHOD__);
        $pageSize = $this->_helper->getEventPageSize();
        $this->_eventService->exportEvents($pageSize);
        return $this;
    }
}