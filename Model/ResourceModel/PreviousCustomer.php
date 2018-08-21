<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 12:54
 */

namespace Contactlab\Hub\Model\ResourceModel;

class PreviousCustomer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('contactlab_hub_previous_customer', 'previous_customer_id');
    }

}