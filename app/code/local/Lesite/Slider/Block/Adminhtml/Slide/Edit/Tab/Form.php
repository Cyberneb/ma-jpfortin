<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slide_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
          'id' => 'edit_form',
          'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
          'method' => 'post',
          'enctype' => 'multipart/form-data'
      ));
      
      $_model = Mage::registry('slide_data');
      $this->setForm($form);

      $fieldset = $form->addFieldset('slide_form', array('legend'=>Mage::helper('slider')->__('Slide information')));
     
      /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field =$fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('slider')->__('Name'),
          'required'  => true,
          'name'      => 'name',
      ));
     
      $fieldset->addField('redirect_to', 'text', array(
          'label'     => Mage::helper('slider')->__('Redirect to'),
          'required'  => true,
          'name'      => 'redirect_to',
      ));
 
      $fieldset->addField('image_alt', 'text', array(
          'label'     => Mage::helper('slider')->__('Description'),
          'required'  => false,
          'name'      => 'image_alt',
      ));
 
      $fieldset->addField('slider_id', 'hidden', array(
          'name'      => 'slider_id',
          'required'  => true,
          'value'     => $_model->getslider_id(),
      ));
      
      $fieldset->addField('position', 'text', array(
          'label'     => Mage::helper('slider')->__('Order'),
          'required'  => false,
          'name'      => 'position',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('slider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('slider')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('slider')->__('Disabled'),
              ),
          ),
      ));
   
//      $fieldset->addType('lookbookimage','Altima_Lookbookslider_Block_Adminhtml_Slide_Edit_Form_Element_Lookbookimage');
//      $fieldset->addField('image_path', 'lookbookimage', array(
//          'label'     => Mage::helper('slider')->__('Image'),
//          'name'      => 'image_path',
//          'required'  => true,       
//      ));
      
      $fieldset->addField('image_path', 'hidden', array(
            'label'    => Mage::helper('slider')->__('Image'),
            'required'  => false,
            'name'     => 'image_path',
       ));

      $image_path = $_model->getImagePath();
      $image_required = empty( $image_path );

      $fieldset->addField('image_file', 'file', array(
            'label'    => Mage::helper('slider')->__('Image'),
            'required'  => $image_required,
            'name'     => 'image_file',
       ));
      
      if ( Mage::getSingleton('adminhtml/session')->getLookbookData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getLookbookData());
          Mage::getSingleton('adminhtml/session')->setLookbookData(null);
      } elseif ( Mage::registry('slide_data') ) {
          $form->setValues(Mage::registry('slide_data')->getData());
      }
      return parent::_prepareForm();
  }
}