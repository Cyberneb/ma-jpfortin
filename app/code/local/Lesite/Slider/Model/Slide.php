<?php
/**
 * Le Site custom slider
 */
class Lesite_Slider_Model_Slide extends Mage_Core_Model_Abstract
{    
    public function _construct()
    {
        parent::_construct();
        $this->_init('slider/slide');
    }

   public function cleanCache()
        {
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
}
