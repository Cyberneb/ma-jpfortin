<?php

/* 
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Adminhtml_Brand_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize tabs and define tabs block settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('brand_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('lesite_brand')->__('Brand Info'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
          'label'     => Mage::helper('lesite_brand')->__('Brand Information'),
          'title'     => Mage::helper('lesite_brand')->__('Brand Information'),
          'content'   => $this->getLayout()->createBlock('lesite_brand/adminhtml_brand_edit_tab_form')->toHtml(),
        ));
 
        $this->addTab('landing_section', array(
          'label'     => Mage::helper('lesite_brand')->__('Landing Page Information'),
          'title'     => Mage::helper('lesite_brand')->__('Landing Page Information'),
          'content'   => $this->getLayout()->createBlock('lesite_brand/adminhtml_brand_edit_tab_landing')->toHtml(),
        ));
        
        $this->addTab('seo_section', array(
          'label'     => Mage::helper('lesite_brand')->__('SEO Information'),
          'title'     => Mage::helper('lesite_brand')->__('SEO Information'),
          'content'   => $this->getLayout()->createBlock('lesite_brand/adminhtml_brand_edit_tab_seo')->toHtml(),
        ));
         
        return parent::_beforeToHtml();
    }
   
}