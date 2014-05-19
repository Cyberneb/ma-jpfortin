<?php

class Lesite_ProductAutocategory_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CATEGORY_PREFIX = 'catalog/navigation/category_';

    const XML_PATH_BESTSELLERS_DAYS = 'catalog/navigation/bestsellers_days';
    const XML_PATH_BESTSELLERS_NUMBER = 'catalog/navigation/bestsellers_number';
    const XML_PATH_BESTSELLERS_SALES_NUMBER = 'catalog/navigation/bestsellers_sales_number';

    /**
     * Get id of category by given code
     *
     * @param string $categoryCode
     * @param int|Mage_Core_Model_Store $store
     * @return int
     */
    public function getCategoryId($categoryCode, $store = null)
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_CATEGORY_PREFIX . $categoryCode, $store);
    }

    /**
     * Get number of last days for bestsellers calculation
     *
     * @param int|Mage_Core_Model_Store $store
     * @return int
     */
    public function getBestsellersDays($store = null)
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_BESTSELLERS_DAYS, $store);
    }

    /**
     * Get number of top sellers to be bestsellers
     *
     * @param int|Mage_Core_Model_Store $store
     * @return int
     */
    public function getBestsellersNumber($store = null)
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_BESTSELLERS_NUMBER, $store);
    }

    /**
     * Get min number of sales for product to mark it as bestsellers
     *
     * @param int|Mage_Core_Model_Store $store
     * @return int
     */
    public function getBestsellersSalesNumber($store = null)
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_BESTSELLERS_SALES_NUMBER, $store);
    }

    /**
     * Get "news_from_date" attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getNewFromAttribute()
    {
        return Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'news_from_date');
    }

    /**
     * Get "news_to_date" attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getNewToAttribute()
    {
        return Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'news_to_date');
    }

    /**
     * Get "special_from_date" attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getSaleFromAttribute()
    {
        return Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'special_from_date');
    }

    /**
     * Get "special_to_date" attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getSaleToAttribute()
    {
        return Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'special_to_date');
    }
}