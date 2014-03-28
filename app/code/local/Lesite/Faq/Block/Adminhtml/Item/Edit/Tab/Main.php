<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Block_Adminhtml_Item_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    
    protected function _prepareForm() {
        $model = Mage::helper('lesite_faq')->getFaqItemInstance();

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
            $fieldset->addField('faq_id', 'hidden', array(
                'name' => 'faq_id',
            ));
        }

        $fieldset->addField('question', 'text', array(
            'name' => 'question',
            'label' => Mage::helper('lesite_faq')->__('Faq Question'),
            'title' => Mage::helper('lesite_faq')->__('Faq Question'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        // Getting categories select values
        $categories_data_select = Mage::getResourceSingleton('faq/category_collection')->getValuesAndLablesForSelectInput();
        
        $fieldset->addField('category', 'select', 
            array (
                'label' => Mage::helper('lesite_faq')->__('Category'), 
                'title' => Mage::helper('lesite_faq')->__('Category'), 
                'name' => 'category', 
                'required' => false,
                'values' => $categories_data_select,
            )
        );
        
        
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
            'label' => Mage::helper('lesite_faq')->__('Faq Order'),
            'title' => Mage::helper('lesite_faq')->__('Faq Order'),
        ));

        $editorConfig = array(
            'add_widgets'          => true, 
            'add_variables'        => true, 
            'add_images'          => true,
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
        );
        
        
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false,'files_browser_window_url'=>$this->getBaseUrl().'admin/cms_wysiwyg_images/index/'));
        
        $fieldset->addField('answer', 'editor', array(
            'name' => 'answer',
            'label' => Mage::helper('lesite_faq')->__('Faq Answer'),
            'title' => Mage::helper('lesite_faq')->__('Faq Answer'),
            'style'    => 'width:500px; height:300px;',
            'config'    => $wysiwygConfig,
            'wysiwyg'   => true,
            'required'  => true,
            'disabled' => $isElementDisabled
        ));

        $status = $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('lesite_faq')->__('Status'),
            'title' => Mage::helper('lesite_faq')->__('Item status'),
            'name' => 'status',
            'required' => true,
            'options' => array(
                '0' => Mage::helper('lesite_faq')->__('Disabled'),
                '1' => Mage::helper('lesite_faq')->__('Enabled'))
        ));

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
        return Mage::helper('lesite_faq')->__('Faq Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('lesite_faq')->__('Faq Info');
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
