<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 28/06/17
 * Time: 15:22
 */

namespace Contactlab\Hub\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Contactlab\Hub\Model\Event\Source\Status as SourceStatus;


class EventStatus extends Column
{

    /**
     * @var UrlInterface
     */
    protected $_optionStatus;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SourceStatus $optionStatus,
        array $components = [],
        array $data = []
    )
    {
        $this->_optionStatus = $optionStatus;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $options = $this->_optionStatus->toOptionArray();
            foreach($dataSource['data']['items'] as & $item) {
                if($item[$fieldName] != '')
                {
                    $status = $item[$fieldName];
                    foreach ($options as $option) {
                        if ($option['value'] == $item[$fieldName])
                        {
                            $status = $option['label'];
                        }
                    }
                    $item[$fieldName] = '<span 
                    class="contactlab-hub-event-status status'.$item[$fieldName].'" 
                    title ="'. $status.'"></span>';
                }
            }
        }
        return $dataSource;
    }
}