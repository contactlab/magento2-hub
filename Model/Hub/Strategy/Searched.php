<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 24/01/18
 * Time: 16:08
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;

class Searched extends HubStrategy
{
    /**
     * Build
     *
     * @return \stdClass
     */
    public function build()
    {
        $hubEvent = new \stdClass();
        $hubEvent->properties = new \stdClass();
        $eventData = json_decode($this->_event->getEventData());
        $hubEvent->properties->keyword = $eventData->keyword;
        $hubEvent->properties->resultCount = $eventData->resultCount;
        return $hubEvent;
    }
}