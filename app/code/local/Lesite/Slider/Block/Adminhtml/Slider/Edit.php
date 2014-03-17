<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Slider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
          
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'slider';
        $this->_controller = 'adminhtml_slider';
        
        $this->_updateButton('save', 'label', Mage::helper('slider')->__('Save Slider'));
        $this->_updateButton('delete', 'label', Mage::helper('slider')->__('Delete Slider'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            Event.observe(window, 'load', function() {
                var userAgent = navigator.userAgent.toLowerCase();
                is_mozilla = /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent );
                if (is_mozilla) setTimeout('resize_contentbefore()', 2000);                        
            });
       
            function resize_contentbefore(){
                obj = $('contentafter_ifr');                                   
                    var height = obj.getHeight()+1;                    
                    $('contentbefore_ifr').setStyle({height: height + 'px'});                 
            }
                                                             
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('slider_data') && Mage::registry('slider_data')->getId() ) {
            return Mage::helper('slider')->__("Edit Slider '%s'", $this->htmlEscape(Mage::registry('slider_data')->getName()));
        } else {
            return Mage::helper('slider')->__('Add Slider');
        }
    }
}