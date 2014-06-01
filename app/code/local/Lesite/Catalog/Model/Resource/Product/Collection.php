<?php

class Lesite_Catalog_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    protected $_withMediaGallery;

    /**
     * Add product images to each item in collection after load
     *
     * @return Lesite_Catalog_Model_Resource_Product_Collection
     */
    public function addMediaGallery()
    {
        $this->_withMediaGallery = true;
        return $this;
    }

    /**
     * Processing collection items after loading
     * Adding image gallery to each proвuct if flag _withMediaGallery is true
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->_withMediaGallery) {
            $adapter = $this->getConnection();

            $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

            // Select gallery images for product
            $select = $adapter->select()
                ->from(
                    array('main'=>$this->getTable('catalog/product_attribute_media_gallery')),
                    array('value_id', 'value AS file', 'entity_id')
                )
                ->joinLeft(
                    array('value' => $this->getTable('catalog/product_attribute_media_gallery_value')),
                    $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$this->getStoreId()),
                    array('label','position','disabled')
                )
                ->joinLeft( // Joining default values
                    array('default_value' => $this->getTable('catalog/product_attribute_media_gallery_value')),
                    'main.value_id = default_value.value_id AND default_value.store_id = 0',
                    array(
                        'label_default' => 'label',
                        'position_default' => 'position',
                        'disabled_default' => 'disabled'
                    )
                )
                ->where('main.attribute_id = ?', $this->getAttribute('media_gallery')->getId())
                ->where('main.entity_id IN (?)', $this->getLoadedIds())
//                ->order($positionCheckSql . ' ' . Varien_Db_Select::SQL_ASC);
                ->order( 'main.value_id ' . Varien_Db_Select::SQL_ASC);

            $images = array();
            foreach ($adapter->fetchAll($select) as $image) {
                if (!isset($images[$image['entity_id']])) {
                    $images[$image['entity_id']] = array();
                }
                $images[$image['entity_id']][] = $image;
            }
            foreach ($this->_items as $item) {
                if (isset($images[$item->getId()])) {
                    $item->setMediaGallery(array('images' => $images[$item->getId()]));
                }
            }
        }
        return $this;
    }
}
