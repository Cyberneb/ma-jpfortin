<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Brand extends Mage_Core_Model_Abstract {
    
    
    //const STATUS_ENABLED = 1;
    //const STATUS_DISABLED = 0;
    
    /* The maximum dimension of the grid thumbnail to resize to */
    //const LARGE_THUMBNAIL_SIZE = 138;
    
    /* Number of brands to show on a single page of collection */
    const PAGINATION_LIMIT = 5;

    /* The maximum dimension of the featured thumbnail to resize to */    
    const SMALL_THUMBNAIL_SIZE = 75;
    
    
    
    protected $_storeId = null;

    public function getStoreId() {
        return $this->_storeId;
    }

    public function setStoreId($storeId) {
        $this->_storeId = $storeId;
        return $this;
    }

    public function getStoreAttributes() {
        return array(
            'title',
            'featured',
            'status',
            'type',
            'short_description',
            'description',
            'landing_page_content',
            'meta_title',
            'meta_keywords',
            'meta_description',
        );
    }

    public function load($id, $field = null) {
        parent::load($id, $field);
        if ($this->getStoreId()) {
            $this->loadStoreValue();
        }
        return $this;
    }

    public function loadStoreValue($storeId = null) {
        if (!$storeId)
            $storeId = $this->getStoreId();
        if (!$storeId)
            return $this;
        $storeValues = Mage::getModel('brand/storevalue')->getCollection()
                ->addFieldToFilter('brand_id', $this->getId())
                ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }

        return $this;
    }

    protected function _beforeSave() {
        if ($storeId = $this->getStoreId()) {
            $defaultBrand = Mage::getModel('brand/brand')->load($this->getId());
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {
                if ($this->getData($attribute . '_default')) {
                    $this->setData($attribute . '_in_store', false);
                } else {
                    $this->setData($attribute . '_in_store', true);
                    $this->setData($attribute . '_value', $this->getData($attribute));
                }
                $this->setData($attribute, $defaultBrand->getData($attribute));
            }
        }
        return parent::_beforeSave();
    }

    protected function _afterSave() {
        if ($storeId = $this->getStoreId()) {
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {
                $attributeValue = Mage::getModel('brand/storevalue')
                        ->loadAttributeValue($this->getId(), $storeId, $attribute);
                
                if ($this->getData($attribute . '_in_store')) {
                    try {
                        $attributeValue->setValue($this->getData($attribute . '_value'))
                                ->save();
                    } catch (Exception $e) {
                        
                    }
                } elseif ($attributeValue && $attributeValue->getId()) {
                    try {
                        $attributeValue->delete();
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
        return parent::_afterSave();
    }
 

    protected function _construct() {
        $this->_init('brand/brand');
    }
    
    public function getActiveCollection() {
        return $this->getCollection('*')->addFieldToFilter('status', '1');
    }

    
    
    public function updateUrlKey() {
        $id = $this->getId();
        $url_key = $this->getData('url_key');

        try {
            if ($this->getStoreId()) {
                $urlrewrite = Mage::getModel("brand/urlrewrite")->loadByIdpath("brand/" . $id, $this->getStoreId());

                $urlrewrite->setData("id_path", "brand/" . $id);
                $urlrewrite->setData("request_path", $this->getData('url_key'));

                $urlrewrite->setData("target_path", 'brand/index/view/id/' . $id);
                $urlrewrite->setData("store_id", $this->getStoreId());
                try {
                    $urlrewrite->save();
                } catch (Exception $e) {
                    
                }
            } else {
                $stores = Mage::getModel('core/store')->getCollection()
                        ->addFieldToFilter('is_active', 1)
                        ->addFieldToFilter('store_id', array('neq' => 0))
                ;
                foreach ($stores as $store) {
                    $rewrite = Mage::getModel("brand/urlrewrite")->loadByIdpath("brand/" . $id, $store->getId());
                    $rewrite->setData("id_path", "brand/" . $id);
                    $rewrite->setData("request_path", $this->getData('url_key'));
                    $rewrite->setData("target_path", 'brand/index/view/id/' . $id);
                    try {
                        $rewrite->setData('store_id', $store->getId())
                                ->save()
                        ;
                    } catch (Exception $e) {
                        
                    }
                }
            }
        } catch (Exception $e) {
            $this->delete();
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }


    public function deleteUrlRewrite() {
        if ($this->getId()) {
            $stores = Mage::getModel('core/store')->getCollection()
                    ->addFieldToFilter('is_active', 1)
            ;
            foreach ($stores as $store) {
                $url = Mage::getModel('brand/urlrewrite')->loadByIdPath('brand/' . $this->getId(), $store->getId());
                if ($url->getId()) {
                    $url->delete();
                }
            }
        }
    }

}