<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 27/06/17
 * Time: 10:11
 */
namespace Contactlab\Hub\Model\Config\Source\Cron;

use Magento\Framework\Option\ArrayInterface;

class Limit implements ArrayInterface
{
    /**
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        for ($i=10; $i< 1001; $i+=10)
        {
            $maxValList[$i] = $i;
        }
        return $maxValList;
    }
}