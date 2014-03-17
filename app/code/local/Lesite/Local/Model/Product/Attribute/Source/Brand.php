<?php

class Lesite_Local_Model_Product_Attribute_Source_Brand extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    const JPF  = 'jpf';
    const LPST = 'lpst';

    public function getAllOptions() {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => '',
                    'label' => Mage::helper('adminhtml')->__('-- Please Select --')
                ),
                array(
                    'label' => Mage::helper('lesite')->__('jpf'),
                    'value' => self::JPF
                ),
                array(
                    'label' => Mage::helper('lesite')->__('lpst'),
                    'value' => self::LPST
                ),
            );
        }
        return $this->_options;
    }

}
