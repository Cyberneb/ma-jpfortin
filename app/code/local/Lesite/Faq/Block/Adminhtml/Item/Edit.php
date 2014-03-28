<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Item_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId   = 'item_id';
        $this->_blockGroup = 'lesite_faq';
        $this->_controller = 'adminhtml_item';

        

        if (Mage::helper('lesite_faq/admin')->isActionAllowed('save')) {
            $this->_updateButton('save', 'label', Mage::helper('lesite_faq')->__('Save Faq Item'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }
        
        if (Mage::helper('lesite_faq/admin')->isActionAllowed('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('lesite_faq')->__('Delete Faq Item'));
        } else {
            $this->_removeButton('delete');
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::helper('lesite_faq')->getFaqItemInstance();
        if ($model->getId()) {
            return Mage::helper('lesite_faq')->__("Edit Faq Item '%s'",
                 $this->escapeHtml($model->getQuestion()));
        } else {
            return Mage::helper('lesite_faq')->__('New Faq Item');
        }
    }
    
    protected function _prepareLayout() {
    parent::_prepareLayout();
    if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
}
}