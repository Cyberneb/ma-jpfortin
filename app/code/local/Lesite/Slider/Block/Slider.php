<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Block_Slider extends Mage_Core_Block_Template
{
    protected $_position = null;
    protected $_isActive = 1;
    protected $_collection;

    public function _getCollection($position = null) {
        if ($this->_collection) {
            return $this->_collection;
        }

      //  $storeId = Mage::app()->getStore()->getId();
        $this->_collection = Mage::getModel('slider/slider')->getCollection()
                ->addEnableFilter($this->_isActive);
       /* if (!Mage::app()->isSingleStoreMode()) {
            $this->_collection->addStoreFilter($storeId);
        }*/

        if (Mage::registry('current_category') && !Mage::registry('current_product')) {
            $_categoryId = Mage::registry('current_category')->getId();
            $this->_collection->addCategoryFilter($_categoryId);
        } elseif (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms') {
            $_pageId = Mage::getBlockSingleton('cms/page')->getPage()->getPageId();
            $this->_collection->addPageFilter($_pageId);
        }
        else
        {
            return false;
        }

        if ($position) {
            $this->_collection->addPositionFilter($position);
        } elseif ($this->_position) {
            $this->_collection->addPositionFilter($this->_position);
        }
        return $this->_collection;
    }
    
    public function _getSlidesCollection($slider_id = null) {
        if (!$slider_id) {
            $collection = null;
        }
        else
        {
            $collection = Mage::getModel('slider/slide')
                ->getCollection()
                ->addFieldToFilter('slider_id', $slider_id)
                ->addEnableFilter($this->_isActive)
                ->setOrder('position', 'ASC');
        }
        return $collection;
    }
    
    public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            if (Mage::registry('current_category') && !Mage::registry('current_product')) {
                $_categoryId = Mage::registry('current_category')->getId();
                $cacheKey = 'POSITION_'.$this->_position.'LAYOUT_'.$this->getNameInLayout().'_CATEGORY'.$_categoryId;
            } elseif (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms') {
                $_pageId = Mage::getBlockSingleton('cms/page')->getPage()->getPageId();
                $cacheKey = 'POSITION_'.$this->_position.'LAYOUT_'.$this->getNameInLayout().'_PAGE'.$_pageId;
            }
        	$this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }
    
}