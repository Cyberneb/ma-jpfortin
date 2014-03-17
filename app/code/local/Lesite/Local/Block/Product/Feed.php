<?php

/**
 * @method string getBrand()
 * @method Lesite_Local_Block_Product_Feed setBrand(string $value)
 * @method int getCategory()
 * @method Lesite_Local_Block_Product_Feed setCategory(int $value)
 * @method string getGender()
 * @method Lesite_Local_Block_Product_Feed setGender(string $value)
 * @method Lesite_Local_Block_Product_Feed setImageWidth(int $value)
 * @method Lesite_Local_Block_Product_Feed setImageHeight(int $value)
 * @method Lesite_Local_Block_Product_Feed setImageAttribute(string $value)
 */
class Lesite_Local_Block_Product_Feed extends Mage_Core_Block_Template {

    /**
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory() {
        $_key = '_category';
        if (!$this->hasData($_key)) {
            $category = Mage::getModel('catalog/category')->load($this->getCategory());
            if (!$category->getId()) {
                $category = NULL;
            }
            $this->setData($_key, $category);
        }
        return $this->getData($_key);
    }

    /**
     *
     * @param string $gender
     * @param int $limit
     * @return array
     */
    protected function _getProductIds($gender, $limit = 3) {
        $products = Mage::getModel('catalog/product')->getCollection();
        /* @var $products Mage_Catalog_Model_Resource_Product_Collection */
        if ($this->_getCategory()) {
            $products->addCategoryFilter($this->_getCategory());
        }
        if ($this->getBrand()) {
            $products->addFieldToFilter('brand', $this->getBrand());
        }
        $products->addFieldToFilter('gender', $gender);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $ids = $products->getAllIds();
        if (count($ids) > $limit) {
            shuffle($ids);
            $result = array_slice($ids, 0, $limit);
        } else {
            $result = $ids;
        }
        return $result;
    }

    /**
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts() {
        $_key = 'products';
        if (!$this->hasData($_key)) {
            switch ($this->getGender()) {
                case 'mixed_man':
                    $ids = array_merge(
                            $this->_getProductIds(Lesite_Local_Model_Product_Attribute_Source_Gender::MAN, 2),
                            $this->_getProductIds(Lesite_Local_Model_Product_Attribute_Source_Gender::WOMAN, 1)
                    );
                    break;
                case 'mixed_woman':
                    $ids = array_merge(
                            $this->_getProductIds(Lesite_Local_Model_Product_Attribute_Source_Gender::MAN, 1),
                            $this->_getProductIds(Lesite_Local_Model_Product_Attribute_Source_Gender::WOMAN, 2)
                    );
                    break;
                case 'man':
                    $ids = $this->_getProductIds(Lesite_Local_Model_Product_Attribute_Source_Gender::MAN);
                    break;
                case 'woman':
                    $ids = $this->_getProductIds(Lesite_Local_Model_Product_Attribute_Source_Gender::WOMAN);
                    break;
                default :
                    $ids = array();
                    break;
            }
            if (!empty($ids)) {
                $products = Mage::getModel('catalog/product')->getCollection();
                /* @var $products Mage_Catalog_Model_Resource_Product_Collection */
                $products->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
                $products->addFieldToFilter('entity_id', array('in' => $ids));
            } else {
                $products = NULL;
            }
            $this->setData($_key, $products);
        }
        return $this->getData($_key);
    }

    /**
     *
     * @return int
     */
    public function getImageWidth() {
        $_key = 'image_width';
        if (!$this->hasData($_key)) {
            $this->setData($_key, 200);
        }
        return $this->getData($_key);
    }

    /**
     * 
     * @return int
     */
    public function getImageHeight() {
        $_key = 'image_height';
        if (!$this->hasData($_key)) {
            $this->setData($_key, $this->getImageWidth());
        }
        return $this->getData($_key);
    }

    /**
     * 
     * @return string
     */
    public function getImageAttribute() {
        $_key = 'image_attribute';
        if (!$this->hasData($_key)) {
            $this->setData($_key, 'image');
        }
        return $this->getData($_key);
    }

    /**
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Helper_Image
     */
    public function getImageUrl($product) {
        $helper = $this->helper('catalog/image');
        /* @var $helper Mage_Catalog_Helper_Image */
        $helper->init($product, $this->getImageAttribute());
        return $helper->resize($this->getImageWidth(), $this->getImageHeight());
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getImageLabel($product) {
        $label = $product->getData($this->getImageAttribute() . '_label');
        if (empty($label)) {
            $label = $product->getName();
        }
        return $label;
    }

    /**
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getPriceHtml($product) {
        return Mage::getBlockSingleton('catalog/product_list')->getPriceHtml($product);
    }

}
