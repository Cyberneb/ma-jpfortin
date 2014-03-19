<?php
/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */


class Lesite_Brand_Model_Type extends Varien_Object
{
    const TYPE_NONE    = 0;
    const TYPE_WOMEN    = 1;
    const TYPE_MEN    = 2;
    const TYPE_BOTH    = 3;
    
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::TYPE_NONE    => Mage::helper('lesite_brand')->__('None'),
            self::TYPE_WOMEN    => Mage::helper('lesite_brand')->__('Women'),
            self::TYPE_MEN   => Mage::helper('lesite_brand')->__('Men'),
            self::TYPE_BOTH   => Mage::helper('lesite_brand')->__('Both')
        );
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}