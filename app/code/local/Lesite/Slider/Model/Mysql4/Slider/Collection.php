<?php

/**
 * Le Site Custom Slider
 */
class Lesite_Slider_Model_Mysql4_Slider_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('slider/slider');
    }

    /**
     * Add Filter by status
     *
     * @param int $status
     * @return Altima_Lookbookslider_Model_Mysql4_Lookbookslider_Collection
     */
    public function addEnableFilter($status = 1) {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

}
