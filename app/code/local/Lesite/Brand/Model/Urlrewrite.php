<?php

/*
 * Brand by Lesite
 * @copyright   Copyright (c) 2014 Lesite http://www.lesite.ca/ *
 */

class Lesite_Brand_Model_Urlrewrite extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('brand/urlrewrite');
    }

    public function loadByIdpath($idPath, $storeId) {
        $model = $this->getCollection()
                ->addFieldToFilter('id_path', $idPath)
                ->addFieldToFilter('store_id', $storeId)
                ->getFirstItem();
        if ($model->getId())
            $this->load($model->getId());
        return $this;
    }

    public function loadByRequestPath($requestPath, $storeId) {
        $collection = $this->getCollection()
                ->addFieldToFilter('request_path', $requestPath);
        if ($storeId)
            $collection->addFieldToFilter('store_id', $storeId);
        if ($collection->getSize()) {
            $model = $collection->getFirstItem();
            if ($model->getId())
                $this->load($model->getId());
        }
        return $this;
    }

}
