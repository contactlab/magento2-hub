<?php
namespace Contactlab\Hub\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\AddressMetadataInterface as Address;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class UpgradeData implements UpgradeDataInterface
{

    const CUSTOMER_TYPE = 'customer_type';
    const STREET_TYPE = 'street_type';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;


    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.9.6', '<='))
        {
            $this->_createCustomerType($setup);
        }
        if (version_compare($context->getVersion(), '0.9.7', '<='))
        {
            $this->_createStreetType($setup);
        }
        $setup->endSetup();
    }


    public function _createCustomerType(ModuleDataSetupInterface $setup)
    {

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        /**
         *  create customer attribute is_vendor
         */
        $customerSetup->addAttribute(Customer::ENTITY, self::CUSTOMER_TYPE,
            [
                'type' => 'varchar',
                'label' => 'CustomerType',
                'input' => 'select',
                "source" => "Contactlab\Hub\Model\Config\Source\CustomerType",
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 210,
                'position' => 210,
                'system' => false,
            ]);

        $customerType = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, self::CUSTOMER_TYPE)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'],
            ]);

        $customerType->save();
        $setup->endSetup();
    }

    public function _createStreetType(ModuleDataSetupInterface $setup)
    {

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Address::ENTITY_TYPE_ADDRESS);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        /**
         *  create customer attribute is_vendor
         */
        $customerSetup->addAttribute(Address::ENTITY_TYPE_ADDRESS, self::STREET_TYPE,
            [
                'type' => 'int',
                'label' => 'Street Type',
                'input' => 'select',
                "source" => "Contactlab\Hub\Model\Config\Source\StreetType",
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 210,
                'position' => 210,
                'system' => false,
            ]);

        $customerType = $customerSetup->getEavConfig()->getAttribute(Address::ENTITY_TYPE_ADDRESS, self::STREET_TYPE)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' =>  ['adminhtml_customer_address', 'customer_address_edit', 'checkout_register', 'adminhtml_checkout'],
            ]);

        $customerType->save();
        $setup->endSetup();
    }
}