<?php

/* 
 * FAQ by Lesite
 * Each line should be prefixed with  * 
 */

 
class Lesite_Faq_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    public function startSetup()
    {
        $this->getConnection()->startSetup();
        return $this;
    }

    public function endSetup()
    {
        $this->getConnection()->endSetup();
        return $this;
    }
}