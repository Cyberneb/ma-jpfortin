<?php

class Lesite_ProductAutocategory_Model_Resource_Product_Bestsellers
    extends Lesite_ProductAutocategory_Model_Resource_Product_Abstract
{
    /**
     * Get array of ids for best sellers products
     *
     * @return array
     */
    public function getProductIds()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select();
        $subselect = $adapter->select();
        $subselectConfig = $adapter->select();
        $subselectSimple = $adapter->select();

        $subselectConfig->from(array('items_conf' => $this->getTable('sales/order_item')), array('items_conf.product_id'))
            ->join(
                array('items' => $this->getTable('sales/order_item')),
                'items_conf.item_id=items.parent_item_id',
                array('sales' => new Zend_Db_Expr('sum(items.qty_ordered)')))
            ->where('items.parent_item_id is not null')
            ->group('items_conf.product_id');

        $this->_applyDateLimitation($subselectConfig)
            ->_applySalesLimitation($subselectConfig);

        $subselectSimple->from(array('items' => $this->getTable('sales/order_item')), array('items.product_id', 'sales' => new Zend_Db_Expr('sum(items.qty_ordered)')))
            ->where('items.parent_item_id is null')
            ->where('items.product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->group('items.product_id');

        $this->_applyDateLimitation($subselectSimple)
            ->_applySalesLimitation($subselectSimple);

        $subselect->union(array($subselectConfig, $subselectSimple));

        $select->from(array('aggr' => new Zend_Db_Expr('('.$subselect.')')))
            ->join(array('e' => $this->getTable('catalog/product')), 'e.entity_id=aggr.product_id', null)
            ->order('aggr.sales DESC');

        $this->_applyNumberLimitation($select);

        return $adapter->fetchCol($select);
    }

    /**
     * Apply limitation by start date
     *
     * @param Varien_Db_Select $select
     * @return Lesite_ProductAutocategory_Model_Resource_Product_Bestsellers
     */
    protected function _applyDateLimitation($select)
    {
        if ($days = $this->_getHelper()->getBestsellersDays()) {
            $startDate = date('Y-m-d', mktime(23, 59, 59, date('n'), date('j')-$days, date('Y')));
            $select->where('items.created_at > ?', $startDate);
        }
        return $this;
    }

    /**
     * Limit number in result
     *
     * @param Varien_Db_Select $select
     * @return Lesite_ProductAutocategory_Model_Resource_Product_Bestsellers
     */
    protected function _applyNumberLimitation($select)
    {
        if ($number = $this->_getHelper()->getBestsellersNumber()) {
            $select->limit($number);
        }
        return $this;
    }

    /**
     * Apply limitation by min ordered qty
     *
     * @param Varien_Db_Select $select
     * @return Lesite_ProductAutocategory_Model_Resource_Product_Bestsellers
     */
    protected function _applySalesLimitation($select)
    {
        if ($salesNumber = $this->_getHelper()->getBestsellersSalesNumber()) {
            $select->having('sales >= ?', $salesNumber);
        }
        return $this;
    }

    /**
     * Get default module hepler
     *
     * @return Lesite_ProductAutocategory_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lesite_productautocategory');
    }
}
