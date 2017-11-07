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
interface HubManagementInterface
{

    /**
     * Compose Event
     *
     * @param EventInterface $event
     * @return \stdClass
     */
    public function composeHubEvent(EventInterface $event);

    /**
     * Send Event
     *
     * @param \stdClass $event
     * @return \stdClass
     */
    public function postEvent(\stdClass $event);

    /**
     * Update Customer
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function updateCustomer(EventInterface $event);

}


