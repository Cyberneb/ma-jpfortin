<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Lesite_Brand_Model_Observer
     */
    public function controller_front_init_routers($observer)
    {
    }
    
    public function brandAttributeUpate($observer){
        $resource = Mage::getResourceModel('brand/brand');
        $attribute = $observer->getAttribute();
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $stores = Mage::getModel('core/store')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('store_id', array('neq' => 0))
        ;
        if($attribute->getAttributeCode() == $attributeCode){
            $optionValue = $attribute->getOption();
            $options = $optionValue['value'];
            $deletes = $optionValue['delete'];
            
            $stores = Mage::getModel('core/store')->getCollection()
                ->addFieldToFilter('is_active',1)
                ->addFieldToFilter('store_id',array('neq'=>0))
                ;
            
            foreach($options as $id=>$option){
                    if(intval($id) == 0){
                        $optionDatabase = $resource->getAttributeOptions($option[0]);
                        $optionDatabase = $optionDatabase[0];
                        if($optionDatabase['option_id'])
                            $id = $optionDatabase['option_id'];
                    }
                    $brand = Mage::getModel('brand/brand')->load($id,'option_id');
                    
                    if(isset($deletes[$id]) && $deletes[$id]){
                        
                        if($brand->getId()){
                            foreach ($stores as $store){
                                $urlRewrite = Mage::getModel('brand/urlrewrite')->loadByIdPath('brand/'.$brand->getId(), $store->getId());
                                if($urlRewrite->getId())
                                    $urlRewrite->delete();
                            }
                            $brand->delete();
                            continue;
                        }
                    }else{
                    //if(!$brand->getId()){
                        $op['store_id'] = 0;
                        $op['option_id'] = $id;
                        $op['value'] = $option[0];
                        Mage::helper('lesite_brand/brand')->insertBrandFromOption($op);
                        foreach($stores as $store){
                            if(isset($option[$store->getId()]) && $option[$store->getId()]){
                                $opStore['store_id'] = $store->getId();
                                $opStore['option_id'] = $id;
                                $opStore['value'] = $option[$store->getId()];

                                if(!$brand->getId())
                                    $brand = Mage::getModel('brand/brand')->load($id, 'option_id');

                                if($brand->getId()){
                                    $brandValue = Mage::getModel('brand/storevalue')->loadAttributeValue($brand->getId(), $store->getId(), 'name');
                                    if ($brandValue->getValue() != $option[$store->getId()]) {
                                        $brandValue->setData('value', $option[$store->getId()])
                                                ->setStoreId($store->getId())
                                                ->save();
                                    }
                                }
                            }
                        }
                }
                //}
                //}
                
            }
        }
    }
    public function updateBrand($observer){
        $controllerAction = $observer->getControllerAction();
        $request = $controllerAction->getRequest();
        $attributeId = $request->getParam('attribute_id');
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        
        $attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
        
        if($attribute->getAttributeCode() == $attributeCode){
            Mage::helper('lesite_brand/brand')->updateBrandsFormCatalog();
        }
        return;
    }
    
    public function saveAdminProduct($observer){
        $product = $observer->getProduct();
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $optionId = $product->getData($attributeCode);
        $oldOptionId = Mage::getModel('catalog/product')->load($product->getId())->getData($attributeCode);
        $oldBrand = Mage::getModel('brand/brand')->load($oldOptionId, 'option_id');  
        $productIds = $oldBrand->getData('product_ids');
        $pos = strpos($productIds, $product->getId());
        if($pos == 0){
            if($product->getId() == $productIds){
                $productIds = '';
            }else{
                $productIds = str_replace($product->getId().',', '', $productIds);
            }
        }elseif($pos > 0){
            $productIds = str_replace(','.$product->getId(), '', $productIds);
        }
        $oldBrand->setProductIds($productIds)->save();
        $newBrand = Mage::getModel('lesite_brand/brand')->load($optionId, 'option_id');
        if($newBrand->getData('product_ids')){
            $newProductIds = $newBrand->getData('product_ids').','.$product->getId();
        }  else {
            $newProductIds = $product->getId();
        }
        $newBrand->setProductIds($newProductIds)->save();
    }
    
    public function productUpdateAttribute($observer){
        $attributesData = $observer->getAttributesData();
        $productIds = $observer->getProductIds();
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $storeId = $observer->getStoreId();
        if(count($productIds)){
            if(isset($attributesData[$attributeCode]) && $attributesData[$attributeCode]){
                $brand = Mage::getModel('brand/brand')->load($attributesData[$attributeCode], 'option_id');
                Mage::helper('lesite_brand/brand')->updateProductsForBrands($productIds, $brand);
            }
        }
    }
    
    public function categoryAdminSave($observer){
        $category = $observer->getCategory();
        $oldIds = $category->getProductCollection()->getAllIds();
        $newIds = $category->getData('posted_products');
    }
    
    public function gotoConfig($observer){
        $controllerAction = $observer->getControllerAction();
        $request = $controllerAction->getRequest();
        $section = $request->getParam('section');
        if($section == 'brand'){
            $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
            Mage::getSingleton('adminhtml/session')->setAttributeCode($attributeCode);
        }
    }
    
    public function configChange($observer){
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $oldCode = Mage::getSingleton('adminhtml/session')->getAttributeCode();
        if($attributeCode != $oldCode){
            $brands = Mage::getModel('brand/brand')->getCollection();
            foreach($brands as $brand){
                $brand->deleteUrlRewrite();
                $brand->delete();
            }
            Mage::helper('lesite_brand/brand')->updateBrandsFormCatalog();
        }
    }
}