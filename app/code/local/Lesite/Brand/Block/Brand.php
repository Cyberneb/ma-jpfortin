<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Brand extends Mage_Catalog_Block_Product_List {
    protected $_productCollection;

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }
    protected function _getCollection() {
        return Mage::getResourceModel('brand/brand_collection');
    }
    public function getAllBrands() {
        $storeId = Mage::app()->getStore()->getId();
        $model = Mage::getModel('brand/brand');
        $collection = $model->getActiveCollection()
                            ->setStoreId($storeId);

        $filter = $this->getRequest()->getParam('filter', false);
        if ($filter && preg_match('/^[a-zA-Z]$/', $filter)) {
            $filter = strtoupper($filter);
            $collection->addFieldToFilter('UPPER(title)', array('like' => "$filter%"));
            Mage::register('brand_filter', $filter);
        }
        
        $collection->addOrder('title', 'ASC');
        return($collection);
    }
    
    public function getBrandsCollection() {
        
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('brand/brand_collection')
                ->setStoreId($storeId)
                ->addFieldToFilter('status', array('eq' => 1));
        
        $cur_page = $this->getRequest()->getParam('page', 1);
        $cur_page = preg_match('/^\d+$/', $cur_page) ? $cur_page: 1;

        $filter = $this->getRequest()->getParam('filter', false);
        if ($filter && preg_match('/^[a-zA-Z]$/', $filter)) {
            $filter = strtoupper($filter);
            $collection->addFieldToFilter('UPPER(title)', array('like' => "$filter%"));
        }
        
        $type = $this->getRequest()->getParam('type', false);
        if ($type) {
            switch($type){
                case "women":
                    $typeInt = '1';
                    break;
                case "men":
                    $typeInt = '2';
                    break;
            }
            $collection->addFieldToFilter('type', array('in' => array($typeInt,3)));
        }
        
        
        $collection->setPageSize(Lesite_Brand_Model_Brand::PAGINATION_LIMIT)
                   ->setCurPage($cur_page);
        
        $collection->addOrder('title', 'ASC');

        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $collection_info = array();
        foreach ($collection as $current_brand) {
            $products = Mage::getModel('catalog/product')
                            ->getCollection('*')
                            ->addAttributeToSelect('id')
                            ->addFieldToFilter('status', '1')
                            ->addAttributeToFilter($attributeCode, $current_brand->getBrandId());
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
            
            $collection_info[] = array('brand' => $current_brand, 'products' => count($products));
        }
        
        return($collection_info);
    }
    
    public function getFeaturedBrands(){
        $storeId = Mage::app()->getStore()->getId();
        $model = Mage::getModel('brand/brand');
        $featured   = $model->getActiveCollection()
                    ->addFieldToFilter('featured', '1')
                    ->setStoreId($storeId)
                    ->addOrder('title', 'ASC');
        $type = $this->getRequest()->getParam('type', false);
        if ($type) {
            switch($type){
                case "women":
                    $typeInt = '1';
                    break;
                case "men":
                    $typeInt = '2';
                    break;
            }
            $featured->addFieldToFilter('type', array('in' => array($typeInt,3)));
        }
        return($featured);
    }

    public function getPaginationLimit() {
        return Lesite_Brand_Model_Brand::PAGINATION_LIMIT;
    }

    
}
