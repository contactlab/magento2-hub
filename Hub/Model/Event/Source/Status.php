<?php

/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 29/06/17
 * Time: 09:15
 */

namespace Contactlab\Hub\Model\Event\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Contactlab\Hub\Api\Data\EventInterface;

class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public static function getOptionArray()
    {
        return [
            EventInterface::EVENT_STATUS_ERROR => __('Error'),
            EventInterface::EVENT_STATUS_UNEXPORTED => __('Collected'),
            EventInterface::EVENT_STATUS_RETRY => __('Retry'),
            EventInterface::EVENT_STATUS_RUNNING => __('Running'),
            EventInterface::EVENT_STATUS_EXPORTED => __('Exported')
        ];
    }
}