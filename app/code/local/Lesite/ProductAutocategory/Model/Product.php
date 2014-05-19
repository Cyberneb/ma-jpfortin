<?php

class Lesite_ProductAutocategory_Model_Product extends Varien_Object
{
    const CATEGORY_NEW_CODE = 'new';
    const CATEGORY_SALE_CODE = 'sale';
    const CATEGORY_BESTSELLERS_CODE = 'bestsellers';
    const CATEGORY_WEB_CODE = 'web';

    /**
     * Assign all product (which must belong to given category) to given category
     *
     * @param string $categoryCode
     * @return Lesite_ProductAutocategory_Model_Product
     */
    public function updateCategoryProducts($categoryCode)
    {
        try {
            $resource = Mage::getResourceSingleton('lesite_productautocategory/product_' . $categoryCode);
            $category = $this->getCategory($categoryCode);
            if ($category && $category->getId()) {
                $category->setPostedProducts($this->_prepareProductIds($resource->getProductIds()))->save();
            }
        } catch (Exception $e) {
        }
        return $this;
    }

    /**
     * Get loaded category model by it's code
     *
     * @param string $categoryCode
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory($categoryCode)
    {
        $categoryId = Mage::helper('lesite_productautocategory')->getCategoryId($categoryCode);
        return Mage::getModel('catalog/category')->load($categoryId);
    }

    /**
     * Turn array of product ids into array of productId=>position
     *
     * @param array $productIds
     * @return array
     */
    protected function _prepareProductIds($productIds)
    {
        $result = array();
        foreach ($productIds as $productId) {
            $result[$productId] = 0;
        }
        return $result;
    }
}
