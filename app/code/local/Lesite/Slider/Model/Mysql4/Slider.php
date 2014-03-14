<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Model_Mysql4_Slider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the slider_id refers to the key field in your database table.
        $this->_init('slider/slider', 'slider_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {
        $select = parent::_getLoadSelect($field, $value, $object);
        //$select->order('name DESC')->limit(1);

        return $select;
    }

    /**
     * Call-back function
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        // Cleanup stats on blog delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete slider/slide
        $adapter->delete($this->getTable('slider/slide'), 'slider_id='.$object->getId());

        return parent::_beforeDelete($object);
    }


}