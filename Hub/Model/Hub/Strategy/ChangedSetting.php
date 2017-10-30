<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 12/06/17
 * Time: 16:52
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;

class ChangedSetting extends HubStrategy
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
        $hubEvent->properties->setting = $eventData->setting;
        $hubEvent->properties->oldValue = $eventData->old_value;
        $hubEvent->properties->newValue = $eventData->new_value;
        return $hubEvent;
    }
}