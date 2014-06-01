<?php

class Lesite_Catalog_Model_Product_Color extends Mage_Core_Model_Abstract
{
    const PRODUCT_COLOR_ATTRIBUTE_CODE = 'color';

    protected $_attribute;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('lesite_catalog/product_color');
    }

    /**
     * Get color attribute object
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute()
    {
        if (is_null($this->_attribute)) {
            $this->_attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, self::PRODUCT_COLOR_ATTRIBUTE_CODE);
        }
        return $this->_attribute;
    }

    /**
     * Specify store
     *
     * @param Mage_Core_Model_Store|int $store
     * @return Lesite_Catalog_Model_Product_Color
     */
    public function setStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }
        $this->_getResource()->setStore($store);
        return $this;
    }

    /**
     * Get full URL of color image
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = '';
        $helper = $this->_getImageHelper();
        if (is_file($helper->getUploadDir() . $this->getImage())) {
            $url = $helper->getUploadUrl() . $this->getImage();
        }
        return $url;
    }

    /**
     * Get product color image helper
     *
     * @return Lesite_Catalog_Helper_Product_Color_Image
     */
    protected function _getImageHelper()
    {
        return Mage::helper('lesite_catalog/product_color_image');
    }
}