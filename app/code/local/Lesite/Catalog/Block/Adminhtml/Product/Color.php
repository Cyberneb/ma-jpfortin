<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'lesite_catalog';
        $this->_controller = 'adminhtml_product_color';
        $this->_headerText = Mage::helper('lesite_catalog')->__('Manage Product Colors');

        $this->removeButton('add');
    }
}