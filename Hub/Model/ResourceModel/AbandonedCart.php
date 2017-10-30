<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 09:13
 */

namespace Contactlab\Hub\Model\ResourceModel;

class AbandonedCart extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('contactlab_hub_abandoned_cart', 'abandoned_cart_id');
    }

}