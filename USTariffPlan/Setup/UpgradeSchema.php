<?php
namespace EWebCartPro\USTariffPlan\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $tables = [
            'quote_address',
            'quote',
            'sales_order',
            'sales_invoice',
            'sales_creditmemo'
        ];
        $columns = [
            'fee' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => '0.00',
                'comment' => 'Fee'
            ],
            'base_fee' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => '0.00',
                'comment' => 'Base Fee'
            ],
            'us_tariff' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => '0.00',
                'comment' => 'US Tariff'
            ],
            'base_us_tariff' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => '0.00',
                'comment' => 'Base US Tariff'
            ]
        ];
        foreach ($tables as $table) {
            $tableName = $setup->getTable($table);
            foreach ($columns as $name => $definition) {
                if (!$connection->tableColumnExists($tableName, $name)) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        $setup->endSetup();
    }
}
