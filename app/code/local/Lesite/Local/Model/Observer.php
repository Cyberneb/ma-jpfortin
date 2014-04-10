<?php

class Lesite_Local_Model_Observer
{
    /**
     * Update layout to add handle in order to current brand selected on event controller_action_layout_load_before
     *
     * @param Varien_Event_Observer $observer
     * @return Lesite_Local_Model_Observer
     */
    public function updateLayoutByBrand(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $controller = $observer->getEvent()->getAction();

        $switcher = Mage::getSingleton('lesite/brand_switcher');
        $switcher->init(Mage::app()->getRequest());

        $layout->getUpdate()->addHandle($switcher->getBrandHandle());
        $layout->getUpdate()->addHandle($switcher->getActionBrandHandle($controller));

        return $this;
    }
}