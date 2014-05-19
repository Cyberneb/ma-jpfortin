<?php

class Lesite_ProductAutocategory_Model_Resource_Product_Sale extends Lesite_ProductAutocategory_Model_Resource_Product_Abstract
{
    /**
     * Get array of ids for products in sale
     *
     * @return array
     */
    public function getProductIds()
    {
        $helper = Mage::helper('lesite_productautocategory');
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();

        $joinConditionSaleFrom = $adapter->quoteInto('e.entity_id=eds.entity_id and eds.store_id = 0 and eds.attribute_id = ?', $helper->getSaleFromAttribute()->getId());
        $joinConditionSaleTo = $adapter->quoteInto('e.entity_id=ede.entity_id and ede.store_id = 0 and ede.attribute_id = ?', $helper->getSaleToAttribute()->getId());

        $select->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(array('eds' => $this->getTable(array('catalog/product', 'datetime'))), $joinConditionSaleFrom, null)
            ->join(array('ede' => $this->getTable(array('catalog/product', 'datetime'))), $joinConditionSaleTo, null)
            ->where('eds.value <= ?', date('Y-m-d'))
            ->where('ede.value >= ?', date('Y-m-d'));

        return $adapter->fetchCol($select);
    }
}
