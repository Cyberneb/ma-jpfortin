<?php

abstract class Lesite_ProductAutocategory_Model_Resource_Product_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Get array of product ids by some condition (optionally belong to all categories with path $categoryPath)
     *
     * @param string $categoryPath
     * @return array
     */
    abstract public function getProductIds($categoryPath = null);
}
