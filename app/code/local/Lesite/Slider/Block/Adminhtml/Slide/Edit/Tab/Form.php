<?php

/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slide_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $_model = Mage::registry('slide_data');
        /* @var $_model Lesite_Slider_Model_Slide */
        $this->setForm($form);

        $fieldset = $form->addFieldset('slide_form', array('legend' => Mage::helper('slider')->__('Slide information')));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field    = $fieldset->addField('store_id', 'multiselect', array(
                'name'     => 'stores[]',
                'label'    => Mage::helper('cms')->__('Store View'),
                'title'    => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'    => $_model->getStores()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'  => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $_model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('name', 'text', array(
            'label'    => Mage::helper('slider')->__('Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $_model->getName()
        ));

        $fieldset->addField('image_path', 'hidden', array(
            'label'    => Mage::helper('slider')->__('Image'),
            'required' => false,
            'name'     => 'image_path',
            'value'    => $_model->getImagePath()
        ));

        $fieldset->addType('image', Mage::getConfig()->getBlockClassName('slider/adminhtml_slide_helper_image'));
        $field = $fieldset->addField('image_file', 'image', array(
            'label'    => Mage::helper('slider')->__('Image'),
            'required' => $_model->getImageUrl() === FALSE,
            'name'     => 'image_file',
            'value'    => $_model->getImageUrl()
        ));


        $fieldset->addField('image_alt', 'text', array(
            'label'    => Mage::helper('slider')->__('Image Description'),
            'required' => false,
            'name'     => 'image_alt',
            'value'    => $_model->getImageAlt()
        ));

        $fieldset->addField('content', 'textarea', array(
            'label'    => Mage::helper('slider')->__('Content'),
            'required' => false,
            'name'     => 'content',
            'value'    => $_model->getContent()
        ));

        foreach (array(1, 2) as $index) {
            $fieldset->addField('button_label_' . $index, 'text', array(
                'label'    => Mage::helper('slider')->__('Button Label %s', $index),
                'required' => false,
                'name'     => 'button_label_' . $index,
                'value'    => $_model->getButtonLabel($index)
            ));

            $fieldset->addField('button_category_id_' . $index, 'text', array(
                'label'    => Mage::helper('slider')->__('Button Category %s', $index),
                'required' => false,
                'name'     => 'button_category_id_' . $index,
                'value'    => $_model->getButtonCategoryId($index)
            ));
        }

        $fieldset->addField('slider_id', 'hidden', array(
            'name'     => 'slider_id',
            'required' => true,
            'value'    => $_model->getSliderId(),
        ));

        $fieldset->addField('position', 'text', array(
            'label'    => Mage::helper('slider')->__('Order'),
            'required' => false,
            'name'     => 'position',
            'value'    => $_model->getPosition()
        ));

        $fieldset->addField('status', 'select', array(
            'label'  => Mage::helper('slider')->__('Status'),
            'name'   => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('slider')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('slider')->__('Disabled'),
                ),
            ),
            'value'  => $_model->getStatus()
        ));

        if (!$_model->getId() && Mage::getSingleton('adminhtml/session')->getLookbookData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getLookbookData());
            Mage::getSingleton('adminhtml/session')->setLookbookData(null);
        }

        return parent::_prepareForm();
    }

}
