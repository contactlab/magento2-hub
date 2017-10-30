<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 21/06/17
 * Time: 14:41
 */

namespace Contactlab\Hub\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.9.1', '<='))
        {
            $this->_addAbandonedCartTable($setup);
        }
        if (version_compare($context->getVersion(), '0.9.5', '<='))
        {
            $this->_addPreviousCustomerTable($setup);
        }
        $setup->endSetup();
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function _addAbandonedCartTable(SchemaSetupInterface $setup)
    {
        $tableName = 'contactlab_hub_abandoned_cart';
        if (!$setup->tableExists($tableName))
        {
            $table = $setup->getConnection()->newTable(
                $setup->getTable($tableName)
            )
                ->addColumn(
                    'abandoned_cart_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Abandoned Cart ID'
                )
                ->addColumn(
                    'quote_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    100,
                    ['nullable' => false],
                    'Quote Id'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Store ID'
                )
                ->addColumn(
                    'email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    [
                        'nullable' => false,
                        'collate' => 'utf8_bin'
                    ],
                    'Email'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                    ],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => '0000-00-00 00:00:00'
                    ],
                    'Created At'
                )
                ->addColumn(
                    'abandoned_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => '0000-00-00 00:00:00'
                    ],
                    'Abandoned At'
                )
                ->addColumn(
                    'remote_ip',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    15,
                    [
                        'nullable' => false,
                        'collate' => 'utf8_bin'
                    ],
                    'Remote IP Address'
                )
                ->addColumn(
                    'is_exported',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    [
                        'nullable' => false,
                        'default' => 0
                    ],
                    'Is Abandoned Cart Exported'
                )
                ->addIndex(
                    $setup->getIdxName($tableName, ['store_id']),
                    ['store_id']
                )
                ->addIndex(
                    $setup->getIdxName($tableName, ['email']),
                    ['email']
                )
                ->setComment('Contactlab Hub Abandoned Cart');

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function _addPreviousCustomerTable(SchemaSetupInterface $setup)
    {
        $tableName = 'contactlab_hub_previous_customer';
        if (!$setup->tableExists($tableName)) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable($tableName)
            )
                ->addColumn(
                    'previous_customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Previous Customer ID'
                )
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    100,
                    ['nullable' => false],
                    'Customer Id'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Store ID'
                )
                ->addColumn(
                    'email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    [
                        'nullable' => false,
                        'collate' => 'utf8_bin'
                    ],
                    'Email'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                    ],
                    'Created At'
                )
                ->addColumn(
                    'remote_ip',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    15,
                    [
                        'nullable' => false,
                        'collate' => 'utf8_bin'
                    ],
                    'Remote IP Address'
                )
                ->addColumn(
                    'is_exported',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    [
                        'nullable' => false,
                        'default' => 0
                    ],
                    'Is Previous Cart Exported'
                )

                ->addIndex(
                    $setup->getIdxName($tableName, ['store_id']),
                    ['store_id']
                )
                ->addIndex(
                    $setup->getIdxName($tableName, ['email']),
                    ['email']
                )

                ->setComment('Contactlab Hub Previous Customer');

            $setup->getConnection()->createTable($table);
        }
    }
}