<?php
/**
 * Created by PhpStorm.
 * User: f.delucia
 * Date: 04/05/17
 * Time: 08:59
 */

namespace Contactlab\Hub\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     *
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();


        $tableName = 'contactlab_hub_event';
        if (!$installer->tableExists($tableName)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Event ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [
                    'nullable' => false,
                    'collate' => 'utf8_bin'
                ],
                'Event Name'
                )
            ->addColumn(
                'model',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [
                    'nullable' => false,
                    'collate' => 'utf8_bin'
                ],
                'Event Model'
            )
            ->addColumn(
                'identity_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [
                    'nullable' => false,
                    'collate' => 'utf8_bin'
                ],
                'Identity Email'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Store ID'
            )
            ->addColumn(
                'need_update_identity',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => 0
                ],
                'Is an Identity to Update?'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                    'nullable' => false,
                    'default' => 0
                ],
                'Status'
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
                'event_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'collate' => 'utf8_bin'
                ],
                'Event Data'
            )
            ->addColumn(
                'exported_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Exported At'
            )
            ->addColumn(
                'hub_event',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [
                    'nullable' => true,
                    'collate' => 'utf8_bin'
                ],
                'Json sent via api to contacthub'
            )
            ->addColumn(
                'session_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'collate' => 'utf8_bin'
                ],
                'Session ID'
            )
            ->addColumn(
                'hub_customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'collate' => 'utf8_bin'
                ],
                'Hub Customer ID'
            )
            ->addColumn(
                'env_user_agent',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'collate' => 'utf8_bin'
                ],
                'User Agent'
            )
            ->addColumn(
                'env_remote_ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                15,
                [
                    'nullable' => true,
                    'collate' => 'utf8_bin'
                ],
                'Remote IP Address'
            )


            ->addIndex(
                $installer->getIdxName($tableName, ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName($tableName, ['identity_email']),
                ['identity_email']
            )

            ->setComment('Contactlab Hub Event');

            $installer->getConnection()->createTable($table);

            /*
            // example to add index
            $installer->getConnection()->addIndex(
                $installer->getTable($tableName),
                $installer->getIdxName(
                    $installer->getTable($tableName),
                    [ 'column1', 'column2', ... ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [ 'column1', 'column2', ... ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
            */
        }

        $tableName = $installer->getTable('newsletter_subscriber');

        $columns = [
            'created_at' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'comment' => 'Created At',
            ],
            'last_subscribed_at' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'comment' => 'Last Subscribed At',
            ],
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $columnName => $definition) {
            $connection->addColumn($tableName, $columnName, $definition);
        }

        $installer->endSetup();
    }
}