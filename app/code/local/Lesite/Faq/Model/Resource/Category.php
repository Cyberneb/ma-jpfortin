<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Model_Resource_Category extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct() {
        $this->_init('faq/category', 'category_id');
    }
    
    public function getName()
    {
        return $this->getCategoryName();
    }
    
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        if (is_null($collection)) {
            $collection = Mage::getSingleton('faq/item')->getCollection()
                ->addCategoryFilter($this);
        
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

}
