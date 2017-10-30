<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class AbandonedCart extends EventStrategy
{

    const HUB_EVENT_NAME = 'abandonedCart';
    const HUB_EVENT_SCOPE = 'admin';

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
            $data['name'] = self::HUB_EVENT_NAME;
            $data['scope'] = self::HUB_EVENT_SCOPE;
            $data['need_update_identity'] = true;
            $data['identity_email'] = $context['email'];
            $data['store_id'] = $context['store_id'];
            $data['created_at'] = $context['abandoned_at'];
            $data['env_remote_ip'] = $context['remote_ip'];
            $eventData = array(
                'type' => 'sale',
                'quote_id' => $context['quote_id']
            );
            $data['event_data'] = $eventData;
        }
        return $data;
    }
}