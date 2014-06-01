<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize product color edit block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_blockGroup = 'lesite_catalog';
        $this->_objectId   = 'color_id';
        $this->_controller = 'adminhtml_product_color';

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'editForm.submit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded color
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getColor()->getOptionId()) {
            return Mage::helper('lesite_catalog')->__("Edit Product Color '%s'", $this->htmlEscape($this->getColor()->getName()));
        } else {
            return Mage::helper('lesite_catalog')->__("New Product Color");
        }
    }

    /**
     * Get current Product Color model
     *
     * @return Lesite_Catalog_Model_Product_Color
     */
    public function getColor()
    {
        return Mage::registry('product_color');
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/product_color/' . $action);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit'
        ));
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
