<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('product_alert_stock'),
        'email',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Email'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('product_alert_stock'),
        'name',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Name'
        )
    );

$installer->run("
    ALTER TABLE {$installer->getTable('product_alert_stock')}
    DROP FOREIGN KEY FK_PRODUCT_ALERT_STOCK_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID
");

$installer->endSetup();