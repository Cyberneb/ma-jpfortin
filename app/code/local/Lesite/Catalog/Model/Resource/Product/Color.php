<?php

class Lesite_Catalog_Model_Resource_Product_Color extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_store;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/attribute_option', 'option_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(
            array('t_i' => $this->getTable('lesite_catalog/color_image')),
            $this->getMainTable() . '.option_id=t_i.color_id',
            array('image', 'hex_code')
        );
        return $select;
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('name', $this->_getColorName($object));

        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveImage($object);

        return parent::_afterSave($object);
    }

    /**
     * Save color image
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesite_Catalog_Model_Resource_Product_Color
     */
    protected function _saveImage($object)
    {
        Mage::helper('lesite_catalog/product_color_image')->upload($object, 'image', false);
        $this->_checkHexCode($object);
        $imageTable = $this->getTable('lesite_catalog/color_image');
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($imageTable, 'color_id')
            ->where('color_id = ?', $object->getId());
        $colorId = $adapter->fetchOne($select);
        if ($colorId) {
            if ($object->getImage() || $object->getHexCode()) {
                $adapter->update($imageTable, array(
                    'image' => $object->getImage(),
                    'hex_code' => $object->getHexCode()
                ), array('color_id = ?' => $object->getId()));
            } else {
                $adapter->delete($imageTable, array('color_id = ?' => $object->getId()));
            }
        } else {
            $adapter->insert($imageTable, array(
                'color_id' => $object->getId(),
                'image' => $object->getImage(),
                'hex_code' => $object->getHexCode(),
            ));
        }

        return $this;
    }

    /**
     * Check hex code to be real hex code
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesite_Catalog_Model_Resource_Product_Color
     */
    protected function _checkHexCode($object)
    {
        if (!preg_match('/^[a-f0-9]{6}$/i', $object->getHexCode())) {
            $object->setHexCode(null);
        }
        return $this;
    }

    /**
     * Get loaded color name for current store
     *
     * @return string
     */
    protected function _getColorName($object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('eav/attribute_option_value'))
            ->where('option_id = ?', $object->getId())
            ->where('store_id in (?)', array(0, (int)$this->getStore()));
        $rowsNames = $adapter->fetchAll($select);

        $defaultColorName = null;
        $colorName = null;
        foreach ($rowsNames as $row) {
            if ($row['store_id'] == 0) {
                $defaultColorName = $row['value'];
            } elseif ($row['store_id'] == $this->getStore()) {
                $colorName = $row['value'];
            }
        }
        return $colorName ? $colorName : $defaultColorName;
    }

    /**
     * Setter for store id
     *
     * @param int $store
     * @return Lesite_Catalog_Model_Resource_Product_Color
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Getter for store id
     *
     * @return int
     */
    public function getStore()
    {
        return $this->_store;
    }
}