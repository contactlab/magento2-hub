<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class OrderCompleted extends EventStrategy
{
    const HUB_EVENT_NAME = 'completedOrder';
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
            $data['name'] = self::HUB_EVENT_NAME;
            $data['scope'] = self::HUB_EVENT_SCOPE;
            $data['need_update_identity'] = true;
            $data['identity_email'] = $context['customer_email'];
            $data['store_id'] = $context['store_id'];
            $data['created_at'] = date('Y-m-d H:i:s');
            if(array_key_exists('updated_at', $context)) {
                $data['created_at'] = $context['updated_at'];
            }
            $data['env_remote_ip'] = $context['remote_ip'];
            $eventData = array(
                'type' => 'sale',
                'increment_id' => $context['increment_id']
            );
            $data['event_data'] = $eventData;
        }
        return $data;
    }
}