<?php

/* 
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 * 
 * Brand installation script
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
 * Creating table lesite_brand˙
 */

$table = $installer->getConnection()->newTable($installer->getTable('brand/brand'))
        ->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
        ), 'Brand id')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Title')
        ->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'URL Key')
        ->addColumn('logo', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Logo')
        ->addColumn('banner', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Banner')
        ->addColumn('website', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Website')
        ->addColumn('short_description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Short Description')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Description')
        
        ->addColumn('landing_page_content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ), 'Landing Page Content')
        ->addColumn('meta_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Meta Title')
        ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Meta Keywords')
        ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Meta Description')
        
        ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_TEXT, 63, array(
        ), 'Option ID')
        ->addColumn('product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Product IDs')
        ->addColumn('featured', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Featured')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Status')
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        ), 'type')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        ), 'Creation Time')
        
        ->setComment('Brand');

$installer->getConnection()->createTable($table);

/**
 * Creating table lesite_brand_category
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('brand/storevalue'))
        ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
        ), 'Value Id')
        ->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Brand Id')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        ), 'Store Id')
        ->addColumn('attribute_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Attribute Code')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Value')
        ->addForeignKey(
        $installer->getFkName(
        'brand/brand',
        'brand_id',
        'brand/storevalue',
        'brand_id'
        ),
        'brand_id', $installer->getTable('brand/brand'), 'brand_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(
        'core/store',
        'store_id',
        'brand/storevalue',
        'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addIndex($installer->getIdxName('brand/storevalue', array('brand_id')),
        array('brand_id'))
        ->addIndex($installer->getIdxName('brand/storevalue', array('store_id')),
        array('store_id'))
        
        ->setComment('Brand Store Value');

$installer->getConnection()->createTable($table);



$installer->endSetup();