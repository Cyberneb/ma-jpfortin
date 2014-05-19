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
     * Get array of product ids by some condition
     *
     * @return array
     */
    abstract public function getProductIds();
}
