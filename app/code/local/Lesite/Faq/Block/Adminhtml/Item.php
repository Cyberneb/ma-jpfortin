<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Item extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'lesite_faq';
        $this->_controller = 'adminhtml_item';
        $this->_headerText = Mage::helper('lesite_faq')->__('FAQs Items');
        $this->_addButtonLabel = Mage::helper('lesite_faq')->__('Add New FAQ Item');
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