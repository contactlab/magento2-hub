<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 30/06/17
 * Time: 10:22
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;

class Product extends HubStrategy
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
        $hubEvent->properties = $eventData;
        return $hubEvent;
    }
}