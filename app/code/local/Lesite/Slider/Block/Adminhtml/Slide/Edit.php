<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slide_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'slider/adminhtml_slide_edit_form';
        $this->_controller = 'adminhtml_slide';
        $slider_id = $this->getRequest()->getParam('slider_id');
        if (!$slider_id) {
            $slider_id = Mage::registry('slide_data')->getData('slider_id');            
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/', array('slider_id' => $slider_id)) . '\')');
        $this->_updateButton('save', 'label', Mage::helper('slider')->__('Save Slide'));
        $this->_updateButton('delete', 'label', Mage::helper('slider')->__('Delete Slide')); 
        $this->_updateButton('delete', 'onclick', 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId),'slider_id' => $slider_id)). '\')');

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
                
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/slider_id/". $slider_id ."');
            }
            ";
    }

    public function getHeaderText()
    {
       $slider_id = $this->getRequest()->getParam('slider_id');
        if (!$slider_id) {
            $slider_id = Mage::registry('slide_data')->getData('slider_id');            
        }
        $slider_name = Mage::getModel('slider/slider')->load($slider_id)->getName();
        
        if( Mage::registry('slide_data') && Mage::registry('slide_data')->getId() ) {
            return Mage::helper('slider')->__("Slider &quot%s&quot. Slide &quot%s&quot.", $slider_name, $this->htmlEscape(Mage::registry('slide_data')->getName()));
        } else {
            return Mage::helper('slider')->__("Slider &quot%s&quot. New Slide.", $slider_name);
        }
    }
}