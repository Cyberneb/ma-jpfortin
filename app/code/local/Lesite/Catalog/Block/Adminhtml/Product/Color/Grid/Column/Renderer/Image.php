<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color_Grid_Column_Renderer_Image
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
        if ($url = $row->getImageUrl()) {
            $out .= '<img src="'.$url.'" width="50" height="30"/>';
        }
        return $out;
    }
}
