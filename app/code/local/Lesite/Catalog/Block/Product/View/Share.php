<?php

class Lesite_Catalog_Block_Product_View_Share extends Mage_Catalog_Block_Product_View
{
    /**
     * Get username for instagram account
     *
     * @return string
     */
    public function getInstagramUserName()
    {
        return Mage::helper('lesite_catalog')->getInstagramUsername();
    }
}