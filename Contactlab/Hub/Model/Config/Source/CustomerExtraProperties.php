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

class CustomerExtraProperties implements ArrayInterface
{

    protected $_customerFactory;
    protected $_addressFactory;

    public function __construct(
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressFactory;
    }

    /**
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = array('label' => __('Select an attribute'), 'value' => '');
        $customerAttributes = $this->_customerFactory->create()->getAttributes();
        $customerAttributesArray = [];
        $exclude = array('email', 'prefix', 'firstname', 'lastname', 'gender', 'dob');
        foreach($customerAttributes as $attribute)
        {
            if(!in_array($attribute->getAttributeCode(), $exclude))
            {
                if($attribute->getFrontendLabel())
                {

                    $customerAttributesArray[] = array(
                        'label' => $attribute->getFrontendLabel(),
                        'value' => $attribute->getAttributeCode()
                    );

                }
            }
        }

        $options[] =
            array(
                'label' => __('Customer Attributes'),
                'value' => $customerAttributesArray
            );
        $addressAttributes = $this->_addressFactory->create()->getAttributes();
        $addressAttributesArray = [];
        $exclude = array('city', 'street', 'region', 'region_id', 'postcode', 'country', 'country_id');
        foreach($addressAttributes as $attribute)
        {
            if(!in_array($attribute->getAttributeCode(), $exclude))
            {
                if($attribute->getFrontendLabel())
                {

                    $addressAttributesArray[] = array(
                        'label' => $attribute->getFrontendLabel(),
                        'value' => $attribute->getAttributeCode()
                    );

                }
            }
        }

        $options[] =
            array(
                'label' => __('Customer Address Attributes'),
                'value' => $addressAttributesArray
            );
        return  $options;
    }
}