<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Model_Resource_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected function _construct() {
        $this->_init('faq/item');
    }

    
    /**
     * Filter for active records in list
     * @param null
     * @return Lesite_Faq_Model_Resource_Item_Collection */
    
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('status', Lesite_Faq_Model_item::STATUS_ENABLED);
        return $this;
    }
    
    /**
     * Add Filter by category
     * 
     * @param int|Lesite_Faq_Model_Category $category Category to be filtered
     * @return Lesite_Faq_Model_Resource_Category_Collection
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Lesite_Faq_Model_Category) {
            $category = $category->getId();
        }
        
        $this->addFieldToFilter('category', $category);
       
        
        return $this;
    }
    
    /**
     * Prepare for displaying in list
     * @param integer $page
     * @return Lesite_Faq_Model_Resource_Faq_Collection */
    public function prepareForList($page) {
        $this->setPageSize(Mage::helper('lesite_faq')->getNewsPerPage());
        $this->setCurPage($page)->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC);
        return $this;
    }

    
    /**
     * Filter for store related records in list
     * @param integer $store
     * @return Lesite_Faq_Model_Resource_Item_Collection */
    
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
