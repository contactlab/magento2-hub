<?php

namespace Contactlab\Hub\Model\Config\Source\Cron;

class Minutes implements \Magento\Framework\Option\ArrayInterface
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
            for ($i=1; $i< 60; $i++)
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