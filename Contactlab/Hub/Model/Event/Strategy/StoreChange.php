<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 12/06/17
 * Time: 16:52
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class StoreChange extends EventStrategy
{
    const HUB_EVENT_NAME = 'changedSetting';
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
            $data['identity_email'] = $context['email'];
            $data['store_id'] = $context['store_id'];
            $data['event_data'] = array(
                'setting' 	=> $context['setting'],
                'old_value' => $context['old_value'],
                'new_value' => $context['new_value']
            );
        }
        return $data;
    }
}