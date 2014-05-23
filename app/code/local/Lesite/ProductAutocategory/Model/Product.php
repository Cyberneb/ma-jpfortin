<?php

class Lesite_ProductAutocategory_Model_Product extends Varien_Object
{
    const CATEGORY_NEW_CODE = 'new';
    const CATEGORY_SALE_CODE = 'sale';
    const CATEGORY_BESTSELLERS_CODE = 'bestsellers';
    const CATEGORY_WEB_CODE = 'web';

    /**
     * Assign all product (which must belong to given category type) to categories of given type
     *
     * @param string $categoryTypeCode
     * @return Lesite_ProductAutocategory_Model_Product
     */
    public function updateCategoriesWithProducts($categoryTypeCode)
    {
        try {
            foreach ($this->_getCategories($categoryTypeCode) as $category) {
                $productIds = $this->_getCategoryProductIds($category, $categoryTypeCode);
                $category->setPostedProducts($productIds)->save();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Get category collection by it's code
     *
     * @param string $categoryTypeCode
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategories($categoryTypeCode)
    {
        $categoryIds = Mage::helper('lesite_productautocategory')->getCategoryIds($categoryTypeCode);
        return Mage::getModel('catalog/category')->getCollection()->addIdFilter($categoryIds);
    }

    /**
     * Get list of product ids for assigning to given category
     *
     * @param Mage_Catalog_Model_Category $category
     * @param string $categoryTypeCode
     * @return array
     */
    protected function _getCategoryProductIds($category, $categoryTypeCode)
    {
        $resource = Mage::getResourceSingleton('lesite_productautocategory/product_' . $categoryTypeCode);
        return $this->_prepareProductIds($resource->getProductIds($this->_getParentTopCategoryPath($category)));
    }

    /**
     * Get path for parent category of top level (Men, Women...)
     *
     * @param type $category
     * @return type
     */
    protected function _getParentTopCategoryPath($category)
    {
        $pathIds = $category->getPathIds();
        $pathArr = array_slice($pathIds, 0, 3);
        return implode('/', $pathArr);
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
