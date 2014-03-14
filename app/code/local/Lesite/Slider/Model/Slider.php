<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Model_Slider extends Mage_Core_Model_Abstract
{   
    const CACHE_TAG              = 'slider';
    protected $_cacheTag         = 'slider';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('slider/slider');
    }
}