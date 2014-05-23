<?php

class Lesite_ProductAutocategory_Model_Cron
{
    /**
     * Check all products by some conditions and assign them to special categories: New Arrivals, Sale, Bestsellers
     */
    public function assingProductsToSpecialCategories()
    {
        $updater = Mage::getModel('lesite_productautocategory/product');
        /* @var $updater Lesite_ProductAutocategory_Model_Product */
        $updater->updateCategoriesWithProducts(Lesite_ProductAutocategory_Model_Product::CATEGORY_NEW_CODE);
        $updater->updateCategoriesWithProducts(Lesite_ProductAutocategory_Model_Product::CATEGORY_SALE_CODE);
        $updater->updateCategoriesWithProducts(Lesite_ProductAutocategory_Model_Product::CATEGORY_BESTSELLERS_CODE);
    }
}