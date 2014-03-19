<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Resource_Urlrewrite extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('brand/urlrewrite', 'url_rewrite_id');
    }
}