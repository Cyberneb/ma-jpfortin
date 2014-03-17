<?php

$installer = Mage::getResourceModel('catalog/setup', $this->_resourceName);
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$attributes = array(
    'gender' => array(
        'label'  => 'Gender',
        'source' => 'lesite/product_attribute_source_gender'
    ),
    'brand'  => array(
        'label'  => 'Brand',
        'source' => 'lesite/product_attribute_source_brand'
    )
);
$position   = 10;

foreach ($attributes as $code => $data) {
    $attr = array_merge($data, array(
        'attribute_model'            => NULL,
        'attribute_set'              => 'Default',
        'backend'                    => '',
        'group'                      => 'Details',
        'type'                       => 'varchar',
        'table'                      => '',
        'frontend'                   => '',
        'input'                      => 'select',
        'frontend_class'             => '',
        'required'                   => FALSE,
        'user_defined'               => FALSE,
        'system'                     => TRUE,
        'default'                    => '',
        'unique'                     => FALSE,
        'note'                       => '',
        'input_renderer'             => NULL,
        'global'                     => TRUE,
        'visible'                    => TRUE,
        'searchable'                 => FALSE,
        'filterable'                 => FALSE,
        'comparable'                 => FALSE,
        'visible_on_front'           => FALSE,
        'is_html_allowed_on_front'   => FALSE,
        'is_used_for_price_rules'    => FALSE,
        'filterable_in_search'       => FALSE,
        'used_in_product_listing'    => TRUE,
        'used_for_sort_by'           => FALSE,
        'is_configurable'            => FALSE,
        'apply_to'                   => '',
        'visible_in_advanced_search' => FALSE,
        'position'                   => $position,
        'wysiwyg_enabled'            => FALSE,
        'used_for_promo_rules'       => FALSE,
    ));
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $code, $attr);
    $position += 10;
}

$installer->endSetup();
