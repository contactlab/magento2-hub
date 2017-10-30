<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Api;

use Contactlab\Hub\Api\EventStrategyInterface;
use Contactlab\Hub\Api\Data\EventInterface;

/**
 * @api
 */
interface EventManagementInterface
{
    /**
     * Collect Event
     *
     * @param StrategyEventInterface $strategy
     * @return EventInterface $event
     */
    public function collectEvent(EventStrategyInterface $strategy);


    /**
     * Send Event
     *
     * @param EventInterface $event
     * @return $this
     */
    public function sendEvent(EventInterface $event);

    /**
     * Export Events
     *
     * @param int $pageSize
     * @return $this
     */
    public function exportEvents($pageSize);


}


