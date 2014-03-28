<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Item_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize tabs and define tabs block settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('faq_item_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('lesite_faq')->__('Faq Item Info'));
    }
   
}