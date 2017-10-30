<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 09:13
 */

namespace Contactlab\Hub\Model\ResourceModel\AbandonedCart;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'abandoned_cart_id';


    protected function _construct()
    {
        $this->_init('Contactlab\Hub\Model\AbandonedCart', 'Contactlab\Hub\Model\ResourceModel\AbandonedCart');
    }

}