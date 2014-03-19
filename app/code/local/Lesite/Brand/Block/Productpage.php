<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Productpage extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Lesite_Brand_Block_Brand
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getProduct(){
        $product = Mage::registry('current_product');
        return $product;
    }
    public function getStoreId(){
        return Mage::app()->getStore()->getId();
    }
    public function getBrand(){
        $brand = Mage::getModel('brand/brand');
        $product = $this->getProduct();
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        if($product->getId()){
            $optionId = $product->getData($attributeCode);
            if($optionId){
                $brand->load($optionId, 'brand_id');
                $brand->setStoreId($this->getStoreId())->load($brand->getId());
            }
        }
        return $brand;
    }
}