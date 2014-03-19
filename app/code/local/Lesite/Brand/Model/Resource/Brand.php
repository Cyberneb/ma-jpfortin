<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Resource_Brand extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct() {
        $this->_init('brand/brand', 'brand_id');
    }
    
    public function addOption($brand){
        $op = Mage::getModel('eav/entity_attribute_option')->load($brand->getOptionId());
        
        //$prefix = Mage::helper('lesite_brand')->getTablePrefix();			
        $prefix = '';
        
        $attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $brandStoreId = 0;
        if($brand->getOptionId()){
            if($brand->getStoreId())
                $brandStoreId = $brand->getStoreId();
            $select = $this->_getReadAdapter()->select()
                ->from(array('eao'=> $prefix .'eav_attribute_option'),array('option_id','eaov.value','eaov.store_id'))
                ->join(array('ea'=> $prefix .'eav_attribute'),'eao.attribute_id=ea.attribute_id',array())
                ->join(array('eaov'=> $prefix .'eav_attribute_option_value'),'eao.option_id=eaov.option_id',array())
                ->where('ea.attribute_code=?',$attributeCode)
                ->where('eao.option_id=?', $brand->getOptionId())
                ->where('eaov.store_id=?', $brandStoreId)
            ;
            $storeValue = $this->_getReadAdapter()->fetchAll($select);
            if(count($storeValue)){
                foreach ($storeValue as $value){
                    if(isset($value['value'])&& $value['value']){
                        if($value['value'] == $brand->getTitle())
                            return ;
                        else{
                            $data = array(
                                'value' => $brand->getTitle()
                            );
                            $where= array(
                                'option_id=?' => $brand->getOptionId(),
                                'store_id=?' => $brandStoreId
                            );
                            $update = $this->_getWriteAdapter()->update($prefix.'eav_attribute_option_value', $data, $where);
                        }
                    }
                }
            }else{
                $data = array(
                    'value' => $brand->getTitle(),
                    'option_id' => $brand->getOptionId(),
                    'store_id' => $brandStoreId
                );
                $update = $this->_getWriteAdapter()->insert($prefix.'eav_attribute_option_value', $data);
            }
        }else{
            $attributeId = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $attributeCode)->getId();
            $setup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('catalog_setup');
            $option['attribute_id'] = $attributeId;
            if($brand->getStoreId())
                $option['value']['option'][$brand->getStoreId()] = $brand->getTitle();
            else {
                $option['value']['option'][0] = $brand->getTitle();
            }
            $setup->addAttributeOption($option);
            //get option id
            $select = $this->_getReadAdapter()->select()
                ->from(array('eao'=> $prefix .'eav_attribute_option'),array('option_id','eaov.value','eaov.store_id'))
                ->join(array('ea'=> $prefix .'eav_attribute'),'eao.attribute_id=ea.attribute_id',array())
                ->join(array('eaov'=> $prefix .'eav_attribute_option_value'),'eao.option_id=eaov.option_id',array())
                ->where('ea.attribute_code=?',$attributeCode)
                ->where('eaov.value=?', $brand->getTitle())
                ->where('eaov.store_id=?', $brandStoreId)
            ;
            $option = $this->_getReadAdapter()->fetchAll($select);
            if(count($option)){
                $optionId = $option[0]['option_id'];
                return $optionId;
            }
        }
        return null;
    }
    
    public function removeOption($brand){
        $op = Mage::getModel('eav/entity_attribute_option')->load($brand->getOptionId());
        $prefix = Mage::helper('lesite_brand')->getTablePrefix();			
		$attributeCode = Mage::helper('lesite_brand/brand')->getAttributeCode();
        $brandStoreId = 0;
        if($brand->getOptionId()){
            if($brand->getStoreId())
                $brandStoreId = $brand->getStoreId();
            $option = Mage::getModel('eav/entity_attribute_option')->load($brand->getOptionId());
            try{
                $option -> delete();
            }  catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
    
    public function getBrandByOption($option)
	{
        $brand = Mage::getModel('brand/brand')
                    ->setStoreId($option['store_id']);
		if(isset($option['option_id']) && $option['option_id']){
            $brand->load($option['option_id'], 'option_id');
        }
		return $brand;
	}

}
