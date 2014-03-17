<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Template_Grid_Renderer_Dimensions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    
    public function _getValue(Varien_Object $row)
    {
        $data = $row->getData();
        $out = '<center>';
        $out .= $data['width']."x".$data['height'];
        $out .= '</center>';
        return $out;

    }
}