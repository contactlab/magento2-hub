<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:57
 */

namespace Contactlab\Hub\Model\ResourceModel\Event;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'event_id';


    protected function _construct()
    {
        $this->_init('Contactlab\Hub\Model\Event', 'Contactlab\Hub\Model\ResourceModel\Event');
    }

}