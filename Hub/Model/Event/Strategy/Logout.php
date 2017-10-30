<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class Logout extends EventStrategy
{
    const HUB_EVENT_NAME = 'loggedOut';
    const HUB_EVENT_SCOPE = 'frontend';

    /**
     * Build
     *
     * @return array
     */
    public function build()
    {
        $data = array();
        if($context = $this->_context)
        {
            $eventData = array();
            $data['name'] = self::HUB_EVENT_NAME;
            $data['scope'] = self::HUB_EVENT_SCOPE;
            $data['identity_email'] = $context['email'];
            $data['store_id'] = $context['store_id'];
            $data['event_data'] = $eventData;
        }
        return $data;
    }
}