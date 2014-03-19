<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Helper_Brand extends Mage_Core_Helper_Abstract {

    protected $_storeId = null;
    protected $_brand_product=null;

    public function getStoreId() {
        if (is_null($this->_storeId))
            $this->_storeId = Mage::app()->getStore()->getId();
        return $this->_storeId;
    }

    public function getAttributeCode() {
        $storeId = $this->getStoreId();
        if(!$storeId)
            $storeId = 0;
        $attributeCode = Mage::getStoreConfig('lesite_brand/general/attribute_code', $storeId);
        return $attributeCode ? $attributeCode : 'manufacturer';
    }

    public function getOptionStore() {
        $arrStore = array();
        $arrOptionStore = array();
        $arrOptionStore[] = array('value' => 0, 'label' => 'admin');
        $collection_store = Mage::getModel('core/store')->getCollection();
        foreach ($collection_store as $store) {
            $arrOptionStore[] = array('value' => $store->getId(), 'label' => $store->getName(),);
        }
        return $arrOptionStore;
    }

    public function getOptionData($option) {
        $storeName = Mage::getModel('core/store')->load($option['store_id'])->getData('name');
        $urlKey = $option['value'];
        $data['title'] = $option['value'];
        $data['website'] = $option['value'];
        $data['short_description'] = $option['value'];
        $data['description'] = $option['value'];
        $data['landing_page_content'] = $option['content'];
        $data['option_id'] = $option['option_id'];
        $data['meta_title'] = $option['value'];
        $data['meta_keywords'] = $option['value'];
        $data['meta_description'] = $option['value'];
        $data['status'] = 1;
        $data['type'] = 1;
        $data['created_time'] = now();
        $data['update_time'] = now();
        $data['url_key'] = Mage::helper('lesite_brand')->refineUrlKey($urlKey);
        return $data;
    }

    public function insertBrandFromOption($option) {
        if (isset($option['store_id'])) {
            $data = $this->getOptionData($option);
            $model = Mage::getModel('brand/brand')->load($option['option_id'], 'option_id');
            $model->addData($data);
            $productIds = $this->getProductIdsByBrand($model);
            if (is_string($productIds))
                $model->setProductIds($productIds);
            $urlKey = $model->getUrlKey();
            $urlRewrite = Mage::getModel('brand/urlrewrite')->loadByRequestPath($urlKey, $option['store_id']);
            if(!$model->getId()){
                if($urlRewrite->getId()){

                    $urlKey = $urlKey.'-2';

                    $model->setData('url_key', $urlKey);
                }
            }
            
            $model->setStoreId($option['store_id'])
                    ->save();
            
            
            //update url_key
           if ($option['store_id'] == 0)
                $model->updateUrlKey();
        }
    }

    public function updateBrandsFormCatalog() {
        $defaultOptionBrands = Mage::getResourceModel('brand/brand')->getCatalogBrand(true);
        $storeOptionBrands = Mage::getResourceModel('brand/brand')->getCatalogBrand(false);
        foreach($defaultOptionBrands as $option){
            $this->insertBrandFromOption($option);
        }
        foreach($storeOptionBrands as $option){
            $defaultBrand = Mage::getModel('brand/brand')->load($option['option_id'], 'option_id');
            $brandValue = Mage::getModel('brand/storevalue')->loadAttributeValue($defaultBrand->getId(), $option['store_id'], 'name');
            if ($brandValue->getValue() != $option['value']) {
                $brandValue->setData('value', $option['value'])
                        ->save();
            }
        }
    }

}