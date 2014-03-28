<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 * 
 * FAQ installation script
 *
 * @author Ehsan Khakbaz
 * 
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

/**
 * Creating table lesite_faq_item
 */

$table = $installer->getConnection()->newTable($installer->getTable('faq/item'))
        ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
        ), 'Item id')
        ->addColumn('question', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ), 'Question')
        ->addColumn('category', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Category id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_TEXT, 63, array(
            'nullable' => true,
            'default' => null,
        ), 'Store View Selector')
        ->addColumn('order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Order')
        ->addColumn('answer', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Answer')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Status')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        ), 'Creation Time')
        
        ->setComment('Faq Item');

$installer->getConnection()->createTable($table);

/**
 * Creating table lesite_faq_category
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('faq/category'))
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
        ), 'Category Id')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ), 'title')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_TEXT, 63, array(
            'nullable' => true,
            'default' => null,
        ), 'Store View Selector')
        ->addColumn('order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Order')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Description')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable' => false,
        'default' => '1'
        ), 'Status')
                
        ->setComment('Faq Category');

$installer->getConnection()->createTable($table);



$installer->endSetup();