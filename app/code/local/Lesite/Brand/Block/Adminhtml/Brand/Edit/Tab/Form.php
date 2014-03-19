<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

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
            'banner_in_store' =>  '',
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
            'legend' => Mage::helper('lesite_brand')->__('Brand Info')
        ));

        
        $fieldset->addField('title', 'text', array(
            'label'        => Mage::helper('lesite_brand')->__('Title'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'title',
            'disabled'  => ($inStore && !$data['title_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="title_default" name="title_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['title_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="title_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));

        $fieldset->addField('url_key', 'text', array(
            'label'        => Mage::helper('lesite_brand')->__('URL Key'),
            'required'    => true,
            'name'        => 'url_key',
            'disabled'  => ($inStore)
        ));
        
        if(isset($data['logo']) && $data['logo'])
        {
            $fieldset->addField('old_logo', 'hidden', array(
                'label'     => Mage::helper('lesite_brand')->__('Current Logo'),
                'required'  => false,
                'name'      => 'old_logo',
                'value'     =>$data['logo'],
            ));
        }	
        $fieldset->addField('logo', 'image', array(
            'label'     =>  Mage::helper('lesite_brand')->__('Logo'),
            'required'  =>  false,
            'name'      =>  'logo',
            /*'disabled'  => ($inStore && !$data['image_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="image_default" name="image_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['image_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="image_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',*/
        ));
        
        if(isset($data['banner']) && $data['banner'])
        {
            $fieldset->addField('old_banner', 'hidden', array(
                'label'     => Mage::helper('lesite_brand')->__('Current Banner'),
                'required'  => false,
                'name'      => 'old_banner',
                'value'     =>$data['banner'],
            ));
         }	
        $fieldset->addField('banner', 'image', array(
            'label'     =>  Mage::helper('lesite_brand')->__('Banner'),
            'required'  =>  false,
            'name'      =>  'banner',
            /*'disabled'  => ($inStore && !$data['thumbnail_image_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="thumbnail_image_default" name="thumbnail_image_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['thumbnail_image_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="thumbnail_image_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',*/
        ));
        
        $fieldset->addField('featured', 'select', array(
            'label'     => Mage::helper('lesite_brand')->__('Featured'),
            'name'      => 'featured',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('lesite_brand')->__('Yes'),
                ),

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('lesite_brand')->__('No'),
                ),
            ),
            'disabled'  => ($inStore && !$data['featured_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="featured_default" name="featured_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['featured_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="featured_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));

        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('lesite_brand')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getModel('brand/status')->getOptionHash(),
            'disabled'  => ($inStore && !$data['status_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['status_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="status_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));
        
        $fieldset->addField('type', 'select', array(
            'label'        => Mage::helper('lesite_brand')->__('Type'),
            'name'        => 'type',
            'values'    => Mage::getModel('brand/type')->getOptionHash(),
            'disabled'  => ($inStore && !$data['type_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="type_default" name="type_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['type_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="type_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));
        
         $fieldset->addField('website', 'text', array(
            'label'        => Mage::helper('lesite_brand')->__('Website'),
            'name'        => 'website',
            'disabled'  => ($inStore)
        ));
        
        $fieldset->addField('short_description', 'editor', array(
            'name'        => 'short_description',
            'label'        => Mage::helper('lesite_brand')->__('Short Description'),
            'title'        => Mage::helper('lesite_brand')->__('Short Description'),
            'style'        => 'width:600px; height:70px;',
            'wysiwyg'    => false,
            'required'    => false,
            'disabled'  => ($inStore && !$data['short_description_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="short_description_default" name="short_description_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['short_description_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="short_description_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));
        
        $fieldset->addField('description', 'editor', array(
            'name'        => 'description',
            'label'        => Mage::helper('lesite_brand')->__('Description'),
            'title'        => Mage::helper('lesite_brand')->__('Description'),
            'style'        => 'width:600px; height:250px;',
            'wysiwyg'    => true,
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(), 
            'required'    => false,
            'disabled'  => ($inStore && !$data['description_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="description_default" name="description_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['description_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="description_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
        ));
        
        if(isset($data['logo']) && $data['logo'])
        {
            $data['old_logo'] =  $data['logo'];
            $data['logo'] =  Mage::helper('lesite_brand')->getUrlLogoPath($data['brand_id']) .'/'. $data['logo'];
        }
        
        if(isset($data['banner']) && $data['banner'])
        {
            $data['old_banner'] =  $data['banner'];
            $data['banner'] =  Mage::helper('lesite_brand')->getUrlBannerPath($data['brand_id']) .'/'. $data['banner'];
        }
        
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
