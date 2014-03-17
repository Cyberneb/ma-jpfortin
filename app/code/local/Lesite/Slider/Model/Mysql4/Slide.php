<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Model_Mysql4_Slide extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('slider/slide', 'slide_id');
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'slide_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('slider/slide_store'), $condition);

        return parent::_beforeDelete($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('slider/slide_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'slide_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'slide_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = array(
                (int) $object->getStoreId(),
                Mage_Core_Model_App::ADMIN_STORE_ID,
            );

            $select->join(
                array('sss' => $this->getTable('slider/slide_store')),
                $this->getMainTable().'.slide_id = sss.slide_id',
                array('store_id')
            )->where('is_active = ?', 1)
            ->where('sss.store_id in (?) ', $stores)
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }

    public function getIsUniqueBlockToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('ss' => $this->getMainTable()))
            ->join(
                array('sss' => $this->getTable('slider/slide_store')),
                'ss.slide_id = sss.slide_id',
                array()
            )->where('sss.store_id IN (?)', $stores);

        if ($object->getId()) {
            $select->where('ss.slide_id <> ?', $object->getId());
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('slider/slide_store'), 'store_id')
            ->where('slide_id = :slide_id');

        $binds = array(
            ':slide_id' => (int) $id
        );

        return $adapter->fetchCol($select, $binds);
    }
}