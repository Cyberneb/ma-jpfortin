<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Adminhtml_Template_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    
    public function _getValue(Varien_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $width = str_replace('px','',$this->getColumn()->getWidth());
        $val = $row->getData($this->getColumn()->getIndex());
        $url = Mage::helper('slider')->getResizedUrl($val,$width);
        $out = '<center>';
        $out .= "<img src=" . $url . " />";
        $out .= '</center>';

        return $out;

    }
}