<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 14/12/17
 * Time: 16:00
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class Register extends EventStrategy
{
    const HUB_EVENT_NAME = 'formCompiled';
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
            //$data['need_update_identity'] = true;
            $data['identity_email'] = $context['email'];
            $data['store_id'] = $context['store_id'];
            if(array_key_exists('remote_ip', $context))
            {
                $data['env_remote_ip'] = $context['remote_ip'];
            }
            $data['event_data'] = $eventData;
        }
        return $data;
    }
}