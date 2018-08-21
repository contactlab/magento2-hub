<?php
namespace Contactlab\Hub\Block\Adminhtml\System\Config\Form\Map;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Customer extends AbstractFieldArray
{

    /**
     * @var hubType
     */
    protected $_hubType = null;

    /**
     * @var magentoAttribute
     */
    protected $_magentoAttribute = null;


    protected function _prepareToRender() {
        $this->addColumn('hub_attribute', ['label' => __('Hub Attribute'), 'type' => 'store']);
        $this->addColumn(
            'hub_type',
            [
                'label' => __('Hub Type'),
                'renderer'  => $this->_getHubTypeRenderer(),
            ]
        );
        $this->addColumn(
            'magento_attribute',
            [
                'label' => __('Magento Attribute'),
                'renderer' => $this->_getMagentoAttributeRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function  _getHubTypeRenderer()
    {
        if (!$this->_hubType) {
            $this->_hubType = $this->getLayout()->createBlock(
                '\Contactlab\Hub\Block\Adminhtml\System\Config\Form\Map\Field\HubType',
                '',
                ['data' => ['is_render_to_js_template' => true]]

            );
        }
        return $this->_hubType;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function  _getMagentoAttributeRenderer()
    {
        if (!$this->_magentoAttribute) {
            $this->_magentoAttribute = $this->getLayout()->createBlock(
                '\Contactlab\Hub\Block\Adminhtml\System\Config\Form\Map\Field\MagentoAttribute',
                '',
                ['data' => ['is_render_to_js_template' => true]]

            );
        }
        return $this->_magentoAttribute;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $hubType = $row->getHubType();
        $options = [];
        if ($hubType) {
            $options['option_' . $this->_getHubTypeRenderer()->calcOptionHash($hubType)]
                = 'selected="selected"';
        }
        $magentoAttribute = $row->getMagentoAttribute();
        if ($magentoAttribute) {
            $options['option_' . $this->_getMagentoAttributeRenderer()->calcOptionHash($magentoAttribute)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

}