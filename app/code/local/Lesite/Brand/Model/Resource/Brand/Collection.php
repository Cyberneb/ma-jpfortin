<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Resource_Brand_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected $_storeId = null;
    protected $_addedTable = array();
    protected $_isGroupSql = false;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('brand/brand');
    }
    
    public function getStoreId(){
        return $this->_storeId;
    }
    
    public function setStoreId($storeId){
        $this->_storeId = $storeId;
        if($this->_storeId){
            $brandValueTitle = Mage::getModel('brand/storevalue')->getCollection()
                    ->addFieldToFilter('store_id',$storeId)
                    ->addFieldToFilter('attribute_code','title')
                    ->getSelect()
                    ->assemble();
            
            
            $this->getSelect()
                    ->joinLeft( 
                        array(
                            'brand_value_title'=>new Zend_Db_Expr("($brandValueTitle)")), 
                            'main_table.brand_id = brand_value_title.brand_id',
                            array('title'=>'IF(brand_value_title.value IS NULL,main_table.title,brand_value_title.value)'));
             
        }
        return $this;
    }
    protected function _before3Load()
    {
        $storeId = $this->getStoreId();
        if($storeId){
            $brandValueName = Mage::getModel('brand/storevalue')->getCollection()
                    ->addFieldToFilter('store_id',$storeId)
                    ->addFieldToFilter('attribute_code','title')
                    ->getSelect()
                    ->assemble();
            /*$this->getSelect()
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->joinLeft(
                        array('brand_value_name'=>new Zend_Db_Expr("($brandValueName)")), 
                        'main_table.brand_id = brand_value_name.brand_id', 
                        array(
                            'name'=>'IF(brand_value_name.value IS NULL,main_table.name,brand_value_name.value)',
                            //'is_featured'=>'IF(brand_value.attribute_code = "is_featured",main_table.is_featured,brand_value.value)',
                            //'page_title'=>'IF(brand_value.value IS NULL,main_table.page_title,brand_value.value)',
                            //'meta_keywords'=>'IF(brand_value.value IS NULL,main_table.meta_keywords,brand_value.value)',
                            //'meta_description'=>'IF(brand_value.value IS NULL,main_table.meta_description,brand_value.value)',
                            //'short_description'=>'IF(brand_value.value IS NULL,main_table.short_description,brand_value.value)',
                            //'description'=>'IF(brand_value.value IS NULL,main_table.description,brand_value.value)',
                            //'status'=>'IF(brand_value.value IS NULL,main_table.status,brand_value.value)',
                        )
                    )
                    ->columns('*')
                ;*/
        }
        return $this;
    }


    public function setIsGroupCountSql($value) {
        $this->_isGroupSql = $value;
        return $this;
    }

    public function getSelectCountSql() {
        if ($this->_isGroupSql) {
            $this->_renderFilters();
            $countSelect = clone $this->getSelect();
            $countSelect->reset(Zend_Db_Select::ORDER);
            $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
                $countSelect->reset(Zend_Db_Select::GROUP);
                $countSelect->distinct(true);
                $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
                $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
            } else {
                $countSelect->columns('COUNT(*)');
            }
            return $countSelect;
        }
        return parent::getSelectCountSql();
    }
    
    
    protected function _afterLoad(){
    	return $this;
    }
    
    public function addFieldToFilter($field, $condition=null) {
        $attributes = array(
            'title',
            'featured',
            'status',
            'type',
            'website',
            'short_description',
            'description',
            'landing_page_content',
            'meta_title',
            'meta_keywords',
            'meta_description'
        );
        $storeId = $this->getStoreId();
        if (in_array($field, $attributes) && $storeId) {
            if (!in_array('brand_'.$field, $this->_addedTable)) {
                $this->getSelect()
                    ->joinLeft(array('brand_'.$field => $this->getTable('brand/storevalue')),
                        "main_table.brand_id = brand_$field.brand_id" .
                        " AND brand_$field.store_id = $storeId" .
                        " AND brand_$field.attribute_code = '$field'",
                        array()
                    );
                $this->_addedTable[] = 'brand_'.$field;
            }
            return parent::addFieldToFilter("IF(brand_$field.value IS NULL, main_table.$field, brand_$field.value)", $condition);
        }
        if ($field == 'store_id') {
            $field = 'main_table.store_id';
        }
        $field = $this->_getMappedField($field);
        if (strpos($field, 'SUM') === false && strpos($field, 'COUNT') === false) {
            $this->_select->where($this->_getConditionSql($field, $condition), null,null);// Varien_Db_Select::TYPE_CONDITION);
        } else {
            $this->_select->having($this->_getConditionSql($field, $condition), null,null);// Varien_Db_Select::TYPE_CONDITION);
        }
        return $this;
    }
    
    
}
