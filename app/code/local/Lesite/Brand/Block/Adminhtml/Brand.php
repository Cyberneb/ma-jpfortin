<?php

/* 
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Adminhtml_Brand extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'lesite_brand';
        $this->_controller = 'adminhtml_brand';
        $this->_headerText = Mage::helper('lesite_brand')->__('Manage Brands');
        $this->_addButtonLabel = Mage::helper('lesite_brand')->__('Add New Brand');
        parent::__construct();

    }
    
    /**
     * Returns the CSS class for the header
     * 
     * Usually 'icon-head' and a more precise class is returned. We return
     * only an empty string to avoid spacing on the left of the header as we
     * don't have an icon.
     * 
     * @return string
     */
    public function getHeaderCssClass()
    {
        return '';
    }
}