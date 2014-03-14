<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Model_Mysql4_Slide_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('slider/slide');
    }
    
    /**
     * Add Filter by status
     *
     * @param int $status
     * @return Altima_Lookbookslider_Model_Mysql4_Slider_Collection
     */
    public function addEnableFilter($status = 1) {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }
}