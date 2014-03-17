<?php

/**
 * Le Site custom slider
 * @method int getSliderId()
 * @method Lesite_Slider_Block_Slider setSliderId(string $value)
 */
class Lesite_Slider_Block_Slider extends Mage_Core_Block_Template {

    /**
     * 
     * @return boolean
     */
    public function canShow() {
        return $this->getSlider()->getStatus() && $this->getSlides()->count();
    }

    /**
     * 
     * @return Lesite_Slider_Model_Slider
     */
    public function getSlider() {
        $_key = 'slider';
        if (!$this->hasData($_key)) {
            $slider = Mage::getModel('slider/slider')->load($this->getSliderId());
            /* @var $slider Lesite_Slider_Model_Slider */
            $this->setData($_key, $slider);
        }
        return $this->getData($_key);
    }

    /**
     *
     * @return Lesite_Slider_Model_Mysql4_Slide_Collection
     */
    public function getSlides() {
        $_key = 'slides';
        if (!$this->hasData($_key)) {
            $slides = $this->getSlider()->getSlides();
            $slides->addStoreFilter();
            $slides->addEnableFilter();
            $slides->setOrder('position', Varien_Data_Collection::SORT_ORDER_ASC);
            $this->setData($_key, $slides);
        }
        return $this->getData($_key);
    }

    /**
     * 
     * @param int $id
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory($id) {
        $category = Mage::getModel('catalog/category')->load($id);
        /* @var $category Mage_Catalog_Model_Category */
        if ($category->getId() && $category->getIsActive()) {
            return $category;
        }
        return FALSE;
    }

}
