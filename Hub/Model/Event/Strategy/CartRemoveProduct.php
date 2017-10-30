<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\Event\Strategy;

use Contactlab\Hub\Model\Event\Strategy as EventStrategy;

class CartRemoveProduct extends EventStrategy
{
    const HUB_EVENT_NAME = 'removedProduct';
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
            $data['identity_email'] = $context['email'];
            $data['name'] = self::HUB_EVENT_NAME;
            $data['scope'] = self::HUB_EVENT_SCOPE;
            $data['store_id'] = $context['store_id'];
            $data['event_data'] = array(
                'product_id' => $context['entity_id'],
                'qty' => $context['qty']
            );
        }
        return $data;
    }
}