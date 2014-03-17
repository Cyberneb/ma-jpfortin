<?php

/**
 * Le Site custom slider
 * @method int getId()
 * @method Lesite_Slider_Model_Slide setId(int $value)
 * @method string getName()
 * @method Lesite_Slider_Model_Slide setName(string $value) 
 * @method string getImagePath()
 * @method Lesite_Slider_Model_Slide setImagePath(string $value)
 * @method string getImageAlt()
 * @method Lesite_Slider_Model_Slide setImageAlt(string $value)
 * @method int getPosition()
 * @method Lesite_Slider_Model_Slide setPosition(int $value)
 * @method int getStatus()
 * @method Lesite_Slider_Model_Slide setStatus(int $value)
 * @method int getSliderId()
 * @method Lesite_Slider_Model_Slide setSliderId(int $value)
 * @method array getStores()
 * @method Lesite_Slider_Model_Slide setStores(array $value)
 * @method string getContent()
 * @method Lesite_Slider_Model_Slide setContent(string $value) 
 * @method Lesite_Slider_Model_Slide setImageUrl(string $value)
 */
class Lesite_Slider_Model_Slide extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('slider/slide');
    }

    public function cleanCache() {
        $cacheTags = Mage::getModel('slider/slider')->getCacheTags();
        Mage::app()->cleanCache($cacheTags);
    }

    protected function _beforeSave() {
        $needle = 'slide_id="' . $this->getSlideId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
            return parent::_beforeSave();
        }
    }

    protected function _beforeDelete() {
        $this->cleanCache();
        return parent::_beforeDelete();
    }

    public function getImageUrl() {
        $_key = 'image_url';
        if (!$this->hasData($_key)) {
            $value = FALSE;
            if ($this->getImagePath()) {
                $value = Mage::getBaseUrl('media') . 'slider' . DS . $this->getImagePath();
            }
            $this->setData($_key, $value);
        }
        return $this->getData($_key);
    }

    public function getButtonLabel($index = 1) {
        return $this->getData('button_label_' . $index);
    }

    public function getButtonCategoryId($index = 1) {
        return $this->getData('button_category_id_' . $index);
    }

}
