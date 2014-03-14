<?php

class Lesite_Core_Model_Observer
{
    /**
     * controller_action_layout_load_before event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function detectAjaxRequest (Varien_Event_Observer $observer)
    {
        if ( Mage::app()->getRequest()->getParam('aspopup') !== null )
        {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('popup');
        }

        return;
    }
}
