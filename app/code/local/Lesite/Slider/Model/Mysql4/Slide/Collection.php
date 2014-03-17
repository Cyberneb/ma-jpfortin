<?php

/**
 * Le Site custom slider
 */
class Lesite_Slider_Model_Mysql4_Slide_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
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
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     *
     * @param mixed $store
     * @return Lesite_Slider_Model_Mysql4_Slide_Collection
     */
    public function addStoreFilter($store = NULL) {
        $adapter  = $this->getResource()->getReadConnection();
        /* @var $adapter Magento_Db_Adapter_Pdo_Mysql */
        $select   = $adapter->select();
        $select->from($this->getResource()->getTable('slider/slide_store'), 'slide_id')
                ->where('store_id IN (?)', array(
                    0,
                    Mage::app()->getStore($store)->getId()
        ));
        $slideIds = $adapter->fetchAll($select);
        $this->addFieldToFilter($this->getResource()->getIdFieldName(), array('in' => $slideIds));
        return $this;
    }

}
