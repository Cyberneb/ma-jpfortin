<?php

class Lesite_Local_Block_Brand_Switcher extends Mage_Core_Block_Template
{
    /**
     * Get list of brands
     *
     * @return array
     */
    public function getBrands()
    {
        return $this->_getSwitcher()->getBrands();
    }

    /**
     * Check whether is given brand code current
     *
     * @return string
     */
    public function isBrandCodeCurrent($brandCode)
    {
        return ($this->_getSwitcher()->getCurrentBrandCode() == $brandCode);
    }

    /**
     * Get url to switch to given brand
     *
     * @param string $brandCode
     * @return string
     */
    public function getSwitchUrl($brandCode)
    {
        $query = array($this->_getSwitcher()->getRequestVarName() => $brandCode);
        return Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $query));
    }

    /**
     * Get initialized switcher object
     *
     * @return Lesite_Local_Model_Brand_Switcher
     */
    protected function _getSwitcher()
    {
        return Mage::getSingleton('lesite/brand_switcher');
    }
}
