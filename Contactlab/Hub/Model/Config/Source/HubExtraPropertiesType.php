<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 27/07/17
 * Time: 10:59
 */

namespace Contactlab\Hub\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\AddressFactory;

class HubExtraPropertiesType implements ArrayInterface
{
    /**
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = array('label' => __('Select an attribute'), 'value' => '');
        $options[] = array('label' => __('Base'), 'value' => 'base');
        $options[] = array('label' => __('Consents'), 'value' => 'consents');
        $options[] = array('label' => __('Extended'), 'value' => 'extended');
        return  $options;
    }
}