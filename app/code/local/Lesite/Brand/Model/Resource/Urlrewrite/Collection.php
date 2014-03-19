<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Resource_Urlrewrite_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brand/urlrewrite');
    }
}