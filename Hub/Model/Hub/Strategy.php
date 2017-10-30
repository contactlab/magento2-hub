<?php

/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 30/06/17
 * Time: 14:23
 */
namespace Contactlab\Hub\Model\Hub;

use Contactlab\Hub\Api\HubStrategyInterface;
use Contactlab\Hub\Api\Data\EventInterface;

abstract class Strategy implements HubStrategyInterface
{

    protected $_event;

    /**
     * Set Event
     *
     * @return $this
     */
    public function setEvent(EventInterface $event)
    {
        $this->_event = $event;
        return $this;
    }

    /**
     * Build
     *
     * @return \stdClass
     */
    abstract public function build();

}