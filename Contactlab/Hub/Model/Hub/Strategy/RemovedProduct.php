<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Hub\Strategy;

use Contactlab\Hub\Model\Hub\Strategy\Product as StrategyProduct;

class RemovedProduct extends StrategyProduct
{
    /**
     * Build
     *
     * @return \stdClass
     */
    public function build()
    {
        $hubEvent = parent::build();
        $eventData = json_decode($this->_event->getEventData());
        $hubEvent->properties->quantity = $eventData->qty;
        return $hubEvent;
    }
}