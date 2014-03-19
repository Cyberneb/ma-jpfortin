<?php

/* 
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Adminhtml_Brand_Renderer_Logo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) {
        $file_name = $row->getLogo();
        $file_id = $row->getId();
        $size = Lesite_Brand_Model_Brand::SMALL_THUMBNAIL_SIZE;
        $file_path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) .'/brand/cache/' . $file_id . DS . $file_name;

        $html = "<div style=\"width:{$size}px; height:{$size}px; display:block;\">";

        if (file_exists($file_path)) {
            $file_uri =  Mage::helper('lesite_brand')->getUrlLogoPath($file_id) .'/'. $file_name;
            $html .= "<img src=\"$file_uri\" />";
        }

        $html .= "</div>";

        return $html;
    }
}
