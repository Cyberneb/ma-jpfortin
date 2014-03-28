<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Model_Item extends Mage_Core_Model_Abstract {
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected function _construct() {
        $this->_init('faq/item');
    }

    /**
     * If object is new adds creation date
     * @return Lesite_Faq_Model_Faq
     */
    protected function _beforeSave() {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            
        } $this->setData('created_at', Varien_Date::now());
        return $this;
    }

}