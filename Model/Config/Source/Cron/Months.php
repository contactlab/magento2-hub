<?php

namespace Contactlab\Hub\Model\Config\Source\Cron;

class Months implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected static $_options;
    
    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!self::$_options) {
            $hours = array();
            for ($i=1; $i < 13; $i++)
            {
                $hours[] = array(
                    'label' => $i,
                    'value' => $i,
                );
            }
            self::$_options = $hours;
        }
        return self::$_options;
    }
}