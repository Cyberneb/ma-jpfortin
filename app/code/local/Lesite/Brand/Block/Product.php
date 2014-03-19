<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Product extends Mage_Catalog_Block_Product_List {

    
    public function getStore() {
        $store = Mage::app()->getStore()->getId();
        return $store;
    }

    protected function _getProductCollection() {
        
        return Mage::getSingleton('brand/layer')->getProductCollection();
        
    }

}
