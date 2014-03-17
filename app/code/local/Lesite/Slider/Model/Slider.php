<?php

/**
 * Le Site custom slider
 * @method string getName()
 * @method Lesite_Slider_Model_Slider setName(string $value)
 * @method boolean getStatus()
 * @method Lesite_Slider_Model_Slider setStatus(boolean $value)
 */
class Lesite_Slider_Model_Slider extends Mage_Core_Model_Abstract {

    const CACHE_TAG = 'slider';

    protected $_cacheTag = 'slider';

    public function _construct() {
        parent::_construct();
        $this->_init('slider/slider');
    }

    /**
     *
     * @return Lesite_Slider_Model_Mysql4_Slide_Collection
     */
    public function getSlides() {
        $collection = Mage::getModel('slider/slide')->getCollection();
        /* @var $collection Lesite_Slider_Model_Mysql4_Slide_Collection */
        $collection->addFieldToFilter('slider_id', $this->getId());
        return $collection;
    }

}
