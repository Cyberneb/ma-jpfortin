<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slide_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('slide_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('slider')->__('Slide information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('slider')->__('Slide information'),
          'title'     => Mage::helper('slider')->__('Slide information'),
          'content'   => $this->getLayout()->createBlock('slider/adminhtml_slide_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}