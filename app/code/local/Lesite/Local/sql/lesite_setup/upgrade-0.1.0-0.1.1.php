<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();
// add category attribute custom_name
$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'custom_name', array(
    'group'         => 'General Information',
    'label'         => 'Custom Name',
    'required'      => false,
    'visible'       => 1,
    'sort_order'    => 100,
));

$installer->endSetup();
