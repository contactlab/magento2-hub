<?php

/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 30/06/17
 * Time: 12:34
 */

namespace Contactlab\Hub\Model\Event;

use Contactlab\Hub\Api\EventStrategyInterface;

abstract class Strategy implements EventStrategyInterface
{
    protected $_context;

    /**
     * Set Context
     *
     * @param $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->_context = $context;
        return $this;
    }

    /**
     * Build
     *
     * @return array
     */
    abstract public function build();
}