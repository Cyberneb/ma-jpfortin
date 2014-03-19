<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_View extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Lesite_Brand_Block_View
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getStoreId(){
        $storeId = Mage::app()->getStore()->getId();
        return $storeId;
    }
    
    public function getBrand(){
        if(!$this->hasData('current_brand')){
            $brandId = $this->getRequest()->getParam('id');
            
            $storeId = Mage::app()->getStore()->getId();
            $brand = Mage::getModel('brand/brand')->setStoreId($storeId)
                    ->load($brandId);

            $this->setData('current_brand', $brand);
        }
        if(is_null(Mage::registry('brand')))
            Mage::register('brand', $brand);
        
        return $this->getData('current_brand');
    }
    
    public function getBrandLogoUrl(){
        $brand = $this->getBrand();
        if($brand->getLogo())
            {
                    $url = Mage::helper('lesite_brand')->getUrlLogo($brand->getId()) .'/'. $brand->getLogo();

                    $img = "<img  src='". $url . "' title='". $brand->getTitle()."' border='0' align='left' style='width:128px;'/>";

                    return $img;
            } else{
                    return null;
            }
    }
    
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }
    
    public function setListCollection() {
		$this->getChild('search_result_list')
           ->setCollection($this->_getProductCollection());
    }
    
    protected function _getProductCollection(){
        return $this->getSearchModel()->getProductCollection();
    }
    
    public function getSearchModel()
    {
        return Mage::getSingleton('brand/layer');
    }
    
}