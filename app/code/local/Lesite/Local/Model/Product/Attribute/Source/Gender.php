<?php

class Lesite_Local_Model_Product_Attribute_Source_Gender extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    const MAN   = 'man';
    const WOMAN = 'woman';

    public function getAllOptions() {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => '',
                    'label' => Mage::helper('adminhtml')->__('-- Please Select --')
                ),
                array(
                    'label' => Mage::helper('lesite')->__('Man'),
                    'value' => self::MAN
                ),
                array(
                    'label' => Mage::helper('lesite')->__('Woman'),
                    'value' => self::WOMAN
                ),
            );
        }
        return $this->_options;
    }

}
