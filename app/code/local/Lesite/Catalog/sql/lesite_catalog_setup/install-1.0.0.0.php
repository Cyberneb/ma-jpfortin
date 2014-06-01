<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'lesite_catalog/color_image'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lesite_catalog/color_image'))
    ->addColumn('color_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Color Id')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Image')
    ->addColumn('hex_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Default HEX code')
    ->addForeignKey($installer->getFkName('lesite_catalog/color_image', 'color_id', 'eav/attribute_option', 'option_id'),
        'color_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Lesite Catalog Product Color Images');
$installer->getConnection()->createTable($table);

$installer->endSetup();

// create attributes
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'size', array(
    'type'                       => 'int',
    'label'                      => 'Size',
    'input'                      => 'select',
    'required'                   => false,
    'searchable'                 => true,
    'filterable'                 => true,
    'apply_to'                   => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'sort_order'                 => 130,
    'user_defined'               => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'features', array(
    'type'                       => 'text',
    'label'                      => 'Features',
    'input'                      => 'textarea',
    'required'                   => false,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'wysiwyg_enabled'            => true,
    'is_html_allowed_on_front'   => true,
    'sort_order'                 => 140,
    'user_defined'               => true
));

// add attributes to set and group
$installer->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, 'Default', 'General', 'size');
$installer->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, 'Default', 'General', 'features');


$productPageShippingInfo = array(
    'title'         => 'Product page - shipping info',
    'identifier'    => 'product_page_shipping_info',
    'content'       => "<h3>Free Shipping</h3><p>All orders of $198 or more. $5 flat rate shipping on all other orders.</p>",
    'is_active'     => 1,
    'stores'        => 0
);

Mage::getModel('cms/block')->setData($productPageShippingInfo)->save();
