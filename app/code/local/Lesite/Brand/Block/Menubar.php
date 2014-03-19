<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Block_Menubar extends Mage_Core_Block_Template {
   
    public function getFeaturedBrands() {
        $storeId = Mage::app()->getStore()->getId();
        $model = Mage::getModel('brand/brand');
        
        $featured = $model->getActiveCollection()
                            ->addFieldToFilter('featured', '1')
                            ->setStoreId($storeId)
                            ->addOrder('title', 'ASC');
        return($featured);
    }
    
}
