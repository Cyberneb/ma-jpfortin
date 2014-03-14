<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slide extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_slide';
    $this->_blockGroup = 'slider/adminhtml_slide_grid';
    $slider_name = Mage::registry('slider_data')->getName();
    $slider_id = Mage::registry('slider_data')->getId();    
    $this->_headerText = Mage::helper('slider')->__("Manage %s slides",$slider_name);
        $this->_addButton('back', array(
            'label'     => Mage::helper('slider')->__("Back to slider list"),
            'onclick'   => 'setLocation(\'' . $this->getUrl('slider/adminhtml_slider') .'\')',
            'class'     => 'back',
    ));

    parent::__construct();
    
    $this->_addButton('add', array(
            'label'     => Mage::helper('slider')->__('Add Slide'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/new/', array('slider_id' => $slider_id)) .'\')',
            'class'     => 'add',
    ));
  }
}