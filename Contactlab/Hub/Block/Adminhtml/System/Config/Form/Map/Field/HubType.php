<?php
namespace Contactlab\Hub\Block\Adminhtml\System\Config\Form\Map\Field;

use Contactlab\Hub\Model\Config\Source\HubExtraPropertiesType;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class Countries
 */
class HubType extends Select
{
    /**
     * @var Country
     */
    private $hubExtraPropertiesType;

    /**
     * Constructor
     *
     * @param Context $context
     * @param HubExtraPropertiesType $hubExtraPropertiesType
     * @param array $data
     */
    public function __construct(
        Context $context,
        HubExtraPropertiesType $hubExtraPropertiesType,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->hubExtraPropertiesType = $hubExtraPropertiesType;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->hubExtraPropertiesType->toOptionArray());
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
