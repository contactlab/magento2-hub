<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 07/11/17
 * Time: 16:39
 */

namespace Contactlab\Hub\Cron;

use Contactlab\Hub\Helper\Data as HubHelper;
use Contactlab\Hub\Api\EventManagementInterface;

class CleanEvent
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
        $this->_eventService->cleanEvents();
        return $this;
    }
}