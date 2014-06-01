<?php

class Lesite_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * Get array of available color options for current configurable product
     *
     * @return array
     */
    public function getAvailableColors()
    {
        $optionProducts = array();
        $predefinedColorId = $this->getRequest()->getParam('color');
        $defaultColorId = $predefinedColorId ? $predefinedColorId : null;
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();
            foreach ($this->getAllowAttributes() as $attribute) {
                if ($attribute->getProductAttribute()->getAttributeCode() == $this->getColorAttributeCode()) {
                    $attributeValue = $product->getData($this->getColorAttributeCode());
                    if (!isset($optionProducts[$attributeValue])) {
                        $optionProducts[$attributeValue] = array();
                    }
                    $optionProducts[$attributeValue][] = $productId;
                    if (!$defaultColorId && $product->getIsDefault()) {
                        $defaultColorId = $product->getData($this->getColorAttributeCode());
                    }
                    break;
                }
            }

        }
        foreach ($this->getAllowAttributes() as $attribute) {
            if ($attribute->getProductAttribute()->getAttributeCode() == $this->getColorAttributeCode()) {
                $prices = $attribute->getPrices();
                if (is_array($prices)) {
                    foreach ($prices as $value) {
                        if (isset($optionProducts[$value['value_index']])) {
                            $optionProducts[$value['value_index']]['id'] = $value['value_index'];
                            $optionProducts[$value['value_index']]['label'] = $value['label'];
                            $swatch = $this->_getSwatch($value['value_index']);
                            if ($swatch && $swatch->getId()) {
                                $optionProducts[$value['value_index']]['image'] = $swatch->getImageUrl();
                                $optionProducts[$value['value_index']]['hex_code'] = $swatch->getHexCode();
                            } else {
                                $optionProducts[$value['value_index']]['image'] = '';
                                $optionProducts[$value['value_index']]['hex_code'] = '';
                            }
                            $optionProducts[$value['value_index']]['default'] = ($value['value_index'] == $defaultColorId);
                        }
                    }
                }
                break;
            }
        }
        $optionProducts = $this->_reorderColorData($optionProducts, $defaultColorId);
        return $optionProducts;
    }

    /**
     * Reorder array to put default color on first place
     *
     * @param array $colorData
     * @param int|null $defaultColorId
     * @return array
     */
    protected function _reorderColorData($colorData, $defaultColorId)
    {
        foreach ($colorData as $colorId => $data) {
            if (!isset($data['id'])) {
                unset($colorData[$colorId]);
            }
        }
        if (isset($colorData[$defaultColorId])) {
            $defaultColorData = $colorData[$defaultColorId];
            unset($colorData[$defaultColorId]);
            $newColorData = array($defaultColorId => $defaultColorData);
            foreach ($colorData as $colorId => $data) {
                $newColorData[$colorId] = $data;
            }
            $colorData = $newColorData;
        } else {
            foreach ($colorData as $colorId => $data) {
                $colorData[$colorId]['default'] = true;
                break;
            }
        }
        return $colorData;
    }

    /**
     * Get product color attribute code
     *
     * @return string
     */
    public function getColorAttributeCode()
    {
        return Lesite_Catalog_Model_Product_Color::PRODUCT_COLOR_ATTRIBUTE_CODE;
    }

    /**
     * Get color swatch object for given color option
     *
     * @param int $optionId
     * @return Lesite_Catalog_Model_Product_Color
     */
    protected function _getSwatch($optionId)
    {
        return $this->_getColorSwatchCollection()->getItemById($optionId);
    }

    /**
     * Get all color swatches for configurable color attribute
     *
     * @return array
     */
    protected function _getColorSwatchCollection()
    {
        if (!$this->hasData('color_swatch_collection')) {
            $this->setData('color_swatch_collection', Mage::getModel('lesite_catalog/product_color')->getCollection());
        }
        return $this->getData('color_swatch_collection');
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        return parent::getAllowProducts();
    }
}