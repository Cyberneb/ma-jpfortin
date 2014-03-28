<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Model_Resource_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected function _construct() {
        $this->_init('faq/category');
    }
    
    /**
     * Filter for active records in list
     * @param null
     * @return Lesite_Faq_Model_Resource_Category_Collection */
    
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('status', Lesite_Faq_Model_Category::STATUS_ENABLED);
        return $this;
    }
    
    /**
     * Prepare select input list for categories with values and titles
     * @param null
     * @return $categories_data_select */
    
    public function getValuesAndLablesForSelectInput() {
        
        $categories_data_select = array();
        $categories_data = $this->getData();
        foreach($categories_data as $category_data){
            $categories_data_select[] = array('value' => $category_data['category_id'], 'label' => $category_data['title']);
        }
        
        return $categories_data_select;
    }
    
    
    /**
     * Prepare for displaying in list
     * @param integer $page
     * @return Lesite_Faq_Model_Resource_Faq_Collection */
    public function prepareForList($page) {
        $this->setPageSize(Mage::helper('lesite_faq')->getNewsPerPage());
        $this->setCurPage($page)->setOrder('category_id', Varien_Data_Collection::SORT_ORDER_DESC);
        return $this;
    }
    
    
    /**
     * Filter for store related records in list
     * @param integer $store
     * @return Lesite_Faq_Model_Resource_Category_Collection */
    
    public function addStoreFilter($store, $withAdmin = true){

        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        if (!is_array($store)) {
            $store = array($store);
        }
        
        $this->addFieldToFilter('store_id', array(
            array('regexp'=>$store), 
            array('eq'=>'0')
        ));

        //$this->addFilter('store_id', array('in' => $store));

        return $this;
    }

}
