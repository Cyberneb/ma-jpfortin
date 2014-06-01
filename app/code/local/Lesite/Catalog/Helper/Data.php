<?php

class Lesite_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_INVENTORY_LEFT_IN_STOCK_ITEMS = 'cataloginventory/options/in_stock_left_qty';
    const XML_PATH_PRODUCT_SHARE_INSTAGRAM_USERNAME = 'catalog/product_share/instagram_username';

    /**
     * Get "only X items left" setting value
     *
     * @param Mage_Core_Model_Store $store
     * @return int
     */
    public function getInStockLeftQty($store = null)
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_INVENTORY_LEFT_IN_STOCK_ITEMS, $store);
    }

    /**
     * Get instagram account username
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getInstagramUsername($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PRODUCT_SHARE_INSTAGRAM_USERNAME, $store);
    }
}
