<?php

class Lesite_Catalog_Block_Adminhtml_Product_Color_Edit_Form_Element_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('lesite_catalog/product_color_image')->getUploadUrl() . $this->getValue();
        }
        return $url;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        $value = $this->getData('value');
        if (is_array($value)) {
            return isset($value['value']) ? $value['value'] : current($value);
        }
        return $value;
    }
}