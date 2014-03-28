<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Category_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    
    protected function _prepareForm() {
        $model = Mage::helper('lesite_faq')->getFaqCategoryInstance();

        /**
         * Checking if user have permissions to save information
         */
        if (Mage::helper('lesite_faq/admin')->isActionAllowed('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('faq_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('lesite_faq')->__('Faq Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', array(
                'name' => 'category_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => Mage::helper('lesite_faq')->__('Category Title'),
            'title' => Mage::helper('lesite_faq')->__('Category Title'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('lesite_faq')->__('Store View'),
                'title' => Mage::helper('lesite_faq')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId(),
            ));
        }

        $fieldset->addField('order', 'text', array(
            'name' => 'order',
            'label' => Mage::helper('lesite_faq')->__('Category Order'),
            'title' => Mage::helper('lesite_faq')->__('Category Order'),
        ));
        
        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('lesite_faq')->__('Category Description'),
            'title' => Mage::helper('lesite_faq')->__('Category Description'),
        ));

        $status = $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('lesite_faq')->__('Status'),
            'title' => Mage::helper('lesite_faq')->__('Category status'),
            'name' => 'status',
            'required' => true,
            'options' => array(
                '1' => Mage::helper('lesite_faq')->__('Enabled'),
                '0' => Mage::helper('lesite_faq')->__('Disabled'))
        ));

        Mage::dispatchEvent('adminhtml_category_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('lesite_faq')->__('Faq Category Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('lesite_faq')->__('Faq Category Info');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden() {
        return false;
    }

}
