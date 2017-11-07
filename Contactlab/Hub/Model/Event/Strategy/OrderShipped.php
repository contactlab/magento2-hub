<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 13/06/17
 * Time: 15:47
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class OrderShipped extends EventStrategy
{
    const HUB_EVENT_NAME = 'orderShipped';
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
            $data['identity_email'] = $context['customer_email'];
            $data['store_id'] = $context['store_id'];
            $data['created_at'] = $context['updated_at'];
            $data['env_remote_ip'] = $context['remote_ip'];
            $eventData = array(
                'type' => 'shipped',
                'shipment_id' => $context['increment_id']
            );
            $data['event_data'] = $eventData;
        }
        return $data;
    }
}