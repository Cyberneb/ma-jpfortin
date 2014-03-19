<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Layer extends Mage_Catalog_Model_Layer {

    public function getProductCollection($brandId = null) {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            if(!$brandId)
            $brandId = Mage::app()->getRequest()->getParam("id");
            $brand = Mage::getModel('brand/brand')->setStoreId(Mage::app()->getStore()->getId())
                ->load($brandId);
            $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
            $collection = Mage::getModel('catalog/product')
                    ->getCollection();
            if($brand->getId())
                $collection->addAttributeToFilter($attributeCode, $brand->getId());
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }
    

}