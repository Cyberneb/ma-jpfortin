<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Adminhtml_Brand_Edit_Tab_Landing extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $dataObj = new Varien_Object(array(
            'store_id' => '',
            'title_in_store' => '',
            'url_key_in_store'   =>  '',
            'logo_in_store' =>  '',
            'logowhite_in_store' =>  '',
            'website_in_store' =>  '',
            'short_description_in_store' =>  '',
            'description_in_store' =>  '',
            'landing_page_content' =>  '',
            'meta_title_in_store' =>  '',
            'meta_keywords_in_store' =>  '',
            'meta_description_in_store' =>  '',
            'featured_in_store' =>  '',
            'status_in_store' => '',
            'type_in_store' => ''
        ));
        
        if (isset($data)) $dataObj->addData($data);
            $data = $dataObj->getData();

        if (Mage::getSingleton('adminhtml/session')->getBrandData()) {
            $data = Mage::getSingleton('adminhtml/session')->getBrandData();
            Mage::getSingleton('adminhtml/session')->setBrandData(null);
        } elseif (Mage::registry('brand_data')) {
            $data = Mage::registry('brand_data')->getData();
        }
        
        $storeId = $this->getRequest()->getParam('store');
        if($storeId)
            $store = Mage::getModel('core/store')->load($storeId);
        else
            $store = Mage::app()->getStore();
        $inStore = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('lesite_brand')->__('Use Default');
        $defaultTitle = Mage::helper('lesite_brand')->__('-- Please Select --');
        $scopeLabel = Mage::helper('lesite_brand')->__('STORE VIEW');
        
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('lesite_brand')->__('Brand Landing Page Info')
        ));

        
        
        $fieldset->addField('landing_page_content', 'editor', array(
            'name'        => 'landing_page_content',
            'label'        => Mage::helper('lesite_brand')->__('Landing Page Content'),
            'title'        => Mage::helper('lesite_brand')->__('Landing Page Content'),
            'style'        => 'width:600px; height:400px;',
            'wysiwyg'    => true,
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'required'    => false,
            'disabled'  => ($inStore && !$data['meta_description_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="description_default" name="meta_description_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['meta_description_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="description_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));
        
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('lesite_brand')->__('Brand Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('lesite_brand')->__('Brand Info');
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
