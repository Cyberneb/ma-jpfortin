<?php

class Lesite_Catalog_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{
    /**
     * Check whether is color images should be displayed instead of single image
     *
     * @return boolean
     */
    public function getCanShowColorImages()
    {
        $result = false;
        $product = $this->getProduct();
        $hasOptions = $product->getTypeInstance(true)->hasOptions($product);
        if ($product->isConfigurable() && $hasOptions) {
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
            foreach ($attributes as $attribute) {
                if ($attribute->getProductAttribute()->getAttributeCode() == $this->getColorAttributeCode()) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
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
     * Get array of base images of associated simple products to current configurable one for each color
     *
     * @return array
     */
    public function getColorImages()
    {
        $colorImages = array();
        $predefinedColorId = $this->getRequest()->getParam('color');
        $defaultColorId = $predefinedColorId ? $predefinedColorId : null;
        if ($this->getProduct()->isConfigurable()) {
            $simpleProductCollection = $this->getProduct()->getTypeInstance(true)->getUsedProducts(null, $this->getProduct());
            $simpleProductCollectionWithGallery = Mage::getResourceModel('lesite_catalog/product_collection')
                ->addFieldToFilter('entity_id', array('in' => $this->_getLoadedProductIds($simpleProductCollection)))
                ->addMediaGallery();
            foreach ($simpleProductCollection as $simpleProduct) {
                if (!$defaultColorId && $simpleProduct->getIsDefault()) {
                    $defaultColorId = $simpleProduct->getColor();
                }
            }
            foreach ($simpleProductCollection as $simpleProduct) {
                if (!array_key_exists($simpleProduct->getColor(), $colorImages)
                    || ($simpleProduct->getImage() && $simpleProduct->getImage() != 'no_selection')
                ) {
                    $colorImages[$simpleProduct->getColor()]['base'] = $simpleProduct->getImage();
                    $colorImages[$simpleProduct->getColor()]['default'] = ($simpleProduct->getColor() == $defaultColorId);
                    if ($simpleProductWithGallery = $simpleProductCollectionWithGallery->getItemById($simpleProduct->getId())) {
                        if ($simpleProductWithGallery->getMediaGalleryImages()) {
                            $colorImages[$simpleProduct->getColor()]['thumbnails'] = $simpleProductWithGallery->getMediaGalleryImages();
                        }
                    }
                }
            }
        }
        $colorImages = $this->_reorderColorImageData($colorImages, $defaultColorId);
        return $colorImages;
    }

    /**
     * Reorder array to put default color image on first place
     *
     * @param array $colorData
     * @param int|null $defaultColorId
     * @return array
     */
    protected function _reorderColorImageData($colorData, $defaultColorId)
    {
        if (isset($colorData[$defaultColorId])) {
            $defaultColorData = $colorData[$defaultColorId];
            unset($colorData[$defaultColorId]);
            $newColorData = array($defaultColorId => $defaultColorData);
            foreach ($colorData as $colorId => $data) {
                $newColorData[$colorId] = $data;
            }
            return $newColorData;
        } else {
            foreach ($colorData as $colorId => $data) {
                $colorData[$colorId]['default'] = true;
                break;
            }
        }
        return $colorData;
    }

    /**
     * Get loaded item ids from list of items
     *
     * @param array $collection
     * @return array
     */
    protected function _getLoadedProductIds($collection)
    {
        $loadedIds = array();
        foreach ($collection as $item) {
            $loadedIds[] = $item->getId();
        }
        return $loadedIds;
    }
}