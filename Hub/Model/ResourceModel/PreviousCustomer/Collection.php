<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 26/06/17
 * Time: 12:54
 */

namespace Contactlab\Hub\Model\ResourceModel\PreviousCustomer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'previous_customer_id';


    protected function _construct()
    {
        $this->_init('Contactlab\Hub\Model\PreviousCustomer', 'Contactlab\Hub\Model\ResourceModel\PreviousCustomer');
    }

}