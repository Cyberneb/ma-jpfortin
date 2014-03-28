<?php

/*
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

class Lesite_Faq_Model_Category extends Mage_Core_Model_Abstract {
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected function _construct() {
        $this->_init('faq/category');
    }

}