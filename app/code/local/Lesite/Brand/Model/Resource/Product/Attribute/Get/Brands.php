<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Resource_Product_Attribute_Get_Brands extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {   
        
        $model = Mage::getModel('brand/brand');
        $brands = $model->getCollection();
        
        if (!$this->_options) {
            $this->_options = array(array('value' => '', 'label' => ''));
            foreach ($brands as $brand) {
                $this->_options[] = array('value' => $brand['brand_id'], 'label' => $brand['title']);
            }
        }
        return $this->_options;
    }
}
