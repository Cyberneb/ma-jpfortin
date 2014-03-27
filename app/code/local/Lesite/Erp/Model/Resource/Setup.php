<?php

/* 
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

 
class Lesite_Brand_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
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