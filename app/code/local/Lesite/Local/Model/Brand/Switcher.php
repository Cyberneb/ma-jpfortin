<?php

class Lesite_Local_Model_Brand_Switcher
{
    /**
     * Code of current brand
     *
     * @var string
     */
    protected $_currentBrandCode;

    /**
     * Param name for brand code in request
     *
     * @var string
     */
    protected $_requestVarName = '__brand';

    /**
     * Cookie name for brand code
     *
     * @var string
     */
    protected $_cookieName = 'brand';

    /**
     * Check current brand, switch to given brand, involve cookie
     *
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function init($request)
    {
        $brandCode = $request->getParam($this->getRequestVarName());
        $cookieBrandCode = $this->_getCookie()->get($this->_cookieName);

        $brands = $this->getBrands();
        if ($brandCode && $brands[$brandCode]) {
            $this->_currentBrandCode = $brandCode;
        } elseif ($cookieBrandCode) {
            $this->_currentBrandCode = $cookieBrandCode;
        } else {
            $this->_currentBrandCode = $this->_getDefaultBrandCode();
        }
        $this->_getCookie()->set($this->_cookieName, $this->_currentBrandCode, true);

        return $this;
    }

    /**
     * Get param name for brand code in request
     *
     * @return string
     */
    public function getRequestVarName()
    {
        return $this->_requestVarName;
    }

    /**
     * Get core cookie model
     *
     * @return Mage_Core_Model_Cookie
     */
    protected function _getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }

    /**
     * Get list of brands to switch
     *
     * @return array
     */
    public function getBrands()
    {
        return array(
            Lesite_Local_Model_Product_Attribute_Source_Brand::JPF => Mage::helper('lesite')->__('JP Fortin'),
            Lesite_Local_Model_Product_Attribute_Source_Brand::LPST => Mage::helper('lesite')->__('Les pieds sur terre')
        );
    }

    /**
     * Get code of default brand
     *
     * @return string
     */
    protected function _getDefaultBrandCode()
    {
        return Lesite_Local_Model_Product_Attribute_Source_Brand::JPF;
    }

    /**
     * Get code of current brand
     *
     * @return string
     */
    public function getCurrentBrandCode()
    {
        return $this->_currentBrandCode;
    }

    /**
     * Get brand specific layout update handle to modify layout according to currently selected brand
     *
     * @return string
     */
    public function getBrandHandle()
    {
        $handle = 'default_jpfortin';
        if ($this->getCurrentBrandCode() == Lesite_Local_Model_Product_Attribute_Source_Brand::LPST) {
            $handle = 'default_lespiedssurterre';
        }
        return $handle;

    }

    /**
     * Get brand and action specific layout update handle to modify layout according to currently selected brand
     * and current controller action
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     * @return string
     */
    public function getActionBrandHandle($controller)
    {
        $fullActionName = $controller->getFullActionName();
        $handle = $fullActionName . '_jpfortin';
        if ($this->getCurrentBrandCode() == Lesite_Local_Model_Product_Attribute_Source_Brand::LPST) {
            $handle = $fullActionName . '_lespiedssurterre';
        }
        return $handle;
    }
}