<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /* @var $model Lesite_Catalog_Model_Product_Color */
        $model = Mage::registry('product_color');

        /* @var $helper Lesite_Catalog_Helper_Data */
        $helper = Mage::helper('lesite_catalog');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        $form->setUseContainer(true);

        $form->setHtmlIdPrefix('color_');

        $fieldset = $form->addFieldset('image_fieldset', array('legend'=>$helper->__('Color Image')));

        $this->_addElementTypes($fieldset);
        if ($model->getId()) {
            $fieldset->addField('option_id', 'hidden', array(
                'name' => 'color_id',
            ));
        }

        $fieldset->addField('image', 'image', array(
            'label'     => $helper->__('Image'),
            'title'     => $helper->__('Product Color Image'),
            'name'      => 'image',
            'required'  => false,
            'disabled'  => $isElementDisabled,
        ));

        $fieldset->addField('hex_code', 'text', array(
            'label'     => $helper->__('HEX code'),
            'title'     => $helper->__('Product Default Background Color'),
            'name'      => 'hex_code',
            'required'  => false,
            'disabled'  => $isElementDisabled,
            'class'     => 'color'
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/product_image/' . $action);
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('lesite_catalog/adminhtml_product_color_edit_form_element_image')
        );
    }
}
