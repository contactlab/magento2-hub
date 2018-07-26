<?php
namespace Contactlab\Hub\Block\Adminhtml\System\Config\Form\Map\Field;

use Contactlab\Hub\Model\Config\Source\CustomerExtraProperties;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class Countries
 */
class MagentoAttribute extends Select
{
    /**
     * @var Country
     */
    private $customerExtraProperties;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CustomerExtraProperties $customerExtraProperties
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerExtraProperties $customerExtraProperties,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->customerExtraProperties = $customerExtraProperties;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->customerExtraProperties->toOptionArray());
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
