<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Api;

use Contactlab\Hub\Api\Data\EventInterface;

/**
 * @api
 */
interface HubStrategyInterface
{
    /**
     * Set Event
     *
     * @param $event
     * @return $this
     */
    public function setEvent(EventInterface $event);

    /**
     * Build
     *
     * @return \stdClass
     */
    public function build();
}