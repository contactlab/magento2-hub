<?php
/**
 * Created by PhpStorm.
 * User: ildelux
 * Date: 07/11/17
 * Time: 09:21
 */

namespace Contactlab\Hub\Model\Config\Source;

use\Magento\Sales\Model\Config\Source\Order\Status;

class OrderStatus extends Status
{
    protected $_stateStatuses = null;
}