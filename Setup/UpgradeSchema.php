<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Shift\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.1.9', '<')) {
            $this->createShiftTable($setup);
            $this->createShiftInOutTable($setup);
            $this->createRetailTransaction($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param OutputInterface      $output
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup, OutputInterface $output)
    {
        $output->writeln('  |__ Create shift table');
        $this->createShiftTable($setup);
        $output->writeln('  |__ Create shift in/out table');
        $this->createShiftInOutTable($setup);
        $output->writeln('  |__ Create retail transaction table');
        $this->createRetailTransaction($setup);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createShiftTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_shift_shift'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_shift_shift')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'outlet_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Outlet Id'
        )->addColumn(
            'register_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Register Id'
        )->addColumn(
            'user_open_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'User Open Id'
        )->addColumn(
            'user_close_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'user close id'
        )->addColumn(
            'user_open_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'User name Open'
        )->addColumn(
            'user_close_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'User name close'
        )->addColumn(
            'open_note',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'User name close'
        )->addColumn(
            'close_note',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'User name close'
        )->addColumn(
            'data',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false,],
            'Note'
        )->addColumn(
            'point_earned',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Point Earn'
        )->addColumn(
            'point_spent',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Point Earn'
        )->addColumn(
            'total_adjustment',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Expected amount'
        )->addColumn(
            'total_expected_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Total Expected'
        )->addColumn(
            'total_counted_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Total Counted'
        )->addColumn(
            'total_net_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Total Counted'
        )->addColumn(
            'take_out_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Take out'
        )->addColumn(
            'start_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Take out'
        )->addColumn(
            'open_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'close_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_open',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        )->addColumn(
            'total_order_tax',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000',],
            'Total order tax'
        )->addColumn(
            'base_total_order_tax',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000',],
            'Base total order tax'
        )->addColumn(
            'detail_tax',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Detail all tax in shift'
        )->addColumn(
            'bank_notes',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Shift bank notes'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createShiftInOutTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_shift_shiftinout'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_shift_shiftinout')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'shift_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Shift Id'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'User Id'
        )->addColumn(
            'user_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'User name'
        )->addColumn(
            'note',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Note'
        )->addColumn(
            'amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Total Expected'
        )->addColumn(
            'is_in',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is In'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => Table::TIMESTAMP_UPDATE,],
            'Modification Time'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createRetailTransaction(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_retail_transaction'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_retail_transaction')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'payment_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Payment Id'
        )->addColumn(
            'shift_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Outlet Id'
        )->addColumn(
            'outlet_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Outlet Id'
        )->addColumn(
            'register_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Register Id'
        )->addColumn(
            'payment_title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Payment title'
        )->addColumn(
            'payment_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Payment type'
        )->addColumn(
            'amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Amount'
        )->addColumn(
            'base_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Amount'
        )->addColumn(
            'is_purchase',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Modification Time'
        )->addColumn(
            'user_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'User name'
        )->addColumn(
            'rwr_transaction_id',
            Table::TYPE_INTEGER,
            12,
            ['nullable' => true],
            'Refund Without Receipt Transaction Id'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
