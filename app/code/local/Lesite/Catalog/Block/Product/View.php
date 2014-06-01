<?php

class Lesite_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Check whether back button can be displayed
     *
     * @return bool
     */
    public function getCanShowBackButton()
    {
        $isOwnHost = Mage::getModel('core/url')->isOwnOriginUrl();
        $hasCategory = $this->getProduct()->getCategory();
        $fromSearch = strstr(Mage::app()->getRequest()->getServer('HTTP_REFERER'), 'catalogsearch');

        return ($isOwnHost && ($hasCategory || $fromSearch));
    }

    /**
     * Get url for back button
     *
     * @return string
     */
    public function getBackButtonLink()
    {
        return Mage::app()->getRequest()->getServer('HTTP_REFERER');
    }

    /**
     * Setter for _priceBlockDefaultTemplate
     *
     * @param string $template
     * @return Lesite_Catalog_Block_Product_View
     */
    public function setPriceBlockDefaultTemplate($template)
    {
        $this->_priceBlockDefaultTemplate = $template;
        return $this;
    }
}
