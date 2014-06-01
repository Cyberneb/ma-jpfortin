<?php

class Lesite_Catalog_Model_Resource_Product_Color_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * flag, indicate that filter by store was applied to current collection
     */
    protected $_storeFilterAdded = false;

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('lesite_catalog/product_color');
    }

    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $attributeId = Mage::getModel('lesite_catalog/product_color')->getAttribute()->getId();
        $select = $this->getSelect();
        $select->where('main_table.attribute_id = ?', $attributeId)
            ->joinLeft(
                array('t_i' => $this->getTable('lesite_catalog/color_image')),
                'main_table.option_id=t_i.color_id',
                array('image', 'hex_code')
            );
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @param int $storeId
     * @return Lesite_Catalog_Model_Resource_Product_Color_Collection
     */
    public function addStoreFilter($storeId = null)
    {
        if (!$this->_storeFilterAdded) {
            if (is_null($storeId)) {
                $storeId = Mage::app()->getStore()->getId();
            }
            $adapter = $this->getConnection();

            $joinConditionName = $adapter->quoteInto('tsn.option_id = main_table.option_id AND tsn.store_id = ?', $storeId);
            $colorNameTable = $this->getTable('eav/attribute_option_value');
            $this->getSelect()
                ->join(
                    array('tdn' => $colorNameTable),
                    'tdn.option_id = main_table.option_id',
                    null)
                ->joinLeft(
                    array('tsn' => $colorNameTable),
                    $joinConditionName,
                    array('name' => $adapter->getCheckSql('tsn.value_id > 0', 'tsn.value', 'tdn.value')))
                ->where('tdn.store_id = ?', 0);
            $this->getSelect()->group('main_table.option_id');
            $this->_storeFilterAdded = true;
        }

        return $this;
    }
}