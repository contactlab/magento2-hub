<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 24/01/18
 * Time: 12:56
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy as HubStrategy;

class ViewedProductCategory extends HubStrategy
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
        $hubEvent->properties->category = $eventData->category;
        return $hubEvent;
    }
}