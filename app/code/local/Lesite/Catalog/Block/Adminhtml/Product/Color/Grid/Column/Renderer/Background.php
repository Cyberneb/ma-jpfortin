<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color_Grid_Column_Renderer_Background
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $out = '';
        if ($url = $row->getHexCode()) {
            $out .= '<span style="padding:0 25px;background-color:#'.$row->getHexCode().';">&nbsp;</span> #'.$row->getHexCode();
        }
        return $out;
    }
}