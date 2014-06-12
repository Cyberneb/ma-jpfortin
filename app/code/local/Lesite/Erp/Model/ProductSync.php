<?php

class Lesite_Erp_Model_ProductSync extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('lesite_erp/product_sync');
    }
    
    public function alreadyRunning()
    {
        $last_update = Mage::getResourceModel('lesite_erp/productSync')
            ->getLastUpdateTime();
        if ( (strtotime($last_update) + 60) > time() )
        {
            return true;
        }
        return false;
    }
    
    public function updateAll()
    {
        $delay = time() + 600; // in seconds
        $last_accessed = Mage::getResourceModel('lesite_erp/productSync')
            ->getLastAccessed();
        while( !empty($last_accessed) && time() < $delay )
        {
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateSku($last_accessed[0]['sku']) )
            {
				$product_info = Mage::getResourceModel('lesite_erp/productSync')->getSyncProductInfo( $last_accessed[0]['sku'] );
                $this->saveProduct( $product_info );
                $last_accessed = Mage::getResourceModel('lesite_erp/productSync')
                    ->getLastAccessed();
                continue;
            }
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateInventory($last_accessed[0]['sku']) )
            {
                $product = Mage::getModel("catalog/product")
                    ->loadByAttribute('sku',$last_accessed[0]['sku']);
                if ( $product ) $this->updateInventoryPosition( $product );

            }
            $last_accessed = Mage::getResourceModel('lesite_erp/productSync')
                ->getLastAccessed();
        }
        $product_data = Mage::getResourceModel('lesite_erp/productSync')
            ->addNewProduct();
        while( !empty($product_data['SKU_SKUID']) && time() < $delay )
        { 
            $this->saveProduct( $product_data );
            $product_data = Mage::getResourceModel('lesite_erp/productSync')
                ->addNewProduct();
        }
        $product_data = Mage::getResourceModel('lesite_erp/productSync')
            ->getNewProducts();
        while( !empty($product_data) && time() < $delay )
        {
            $product_data = Mage::getResourceModel('lesite_erp/productSync')
                ->getNewProducts();
        } 
        $product_data = Mage::getResourceModel('lesite_erp/productSync')
            ->addNewProduct();
        while( !empty($product_data['SKU_SKUID']) && time() < $delay )
        { 
            $this->saveProduct( $product_data );
            $product_data = Mage::getResourceModel('lesite_erp/productSync')
                ->addNewProduct();
        }
        $daily_update = Mage::getResourceModel('lesite_erp/productSync')
            ->getDailyUpdate();
        while( !empty($daily_update) && time() < $delay  )
        {
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateSku($daily_update[0]['sku']) )
            {
                $product_info = Mage::getResourceModel('lesite_erp/productSync')->getSyncProductInfo( $daily_update[0]['sku'] );
                $this->saveProduct( $product_info );
                $daily_update = Mage::getResourceModel('lesite_erp/productSync')
                    ->getDailyUpdate();
                continue;
            }
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateInventory($daily_update[0]['sku']) )
            {
                $product = Mage::getModel("catalog/product")
                    ->loadByAttribute('sku',$daily_update[0]['sku']);
                if ( $product ) $this->updateInventoryPosition( $product );
            }
            $daily_update = Mage::getResourceModel('lesite_erp/productSync')
                ->getDailyUpdate();
        }
        if( time() < $delay )
        {
            Mage::getSingleton("core/session")->setSmallestSku(0);
			$socket = fsockopen($_SERVER['HTTP_HOST'],80,$errorno,$errorstr,10);
			if ( $socket )
			{
				$socketdata = "GET /erp/test/reindex HTTP 1.1\r\nHost: "
					. $_SERVER['HTTP_HOST'] . "\r\nConnection: Close\r\n\r\n";
				fwrite($socket, $socketdata);
				fclose($socket);
			}
            return true;
        }
        return false;
    }
    
    // store id : admin = 0, jj_en = 1, jj_fr = 2, lpst_en = 3, lpst_fr = 4    
    protected function getCategoryId( $store_id, $category, $sub_category )
    {
        $parentCategory = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($store_id)
            ->addFieldToFilter('name', $category)
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();
        $parentId = $parentCategory->getId();
        if (empty($parentId))
        {
            $root_id = Mage::app()->getStore()->getRootCategoryId();
 			$rootPath = '1/'.$root_id;
            $parentCategory = Mage::getModel('catalog/category');
            $parentCategory->setName($category);
            $parentCategory->setIsActive(1);
            $parentCategory->setDisplayMode('PRODUCTS');
            $parentCategory->setPath($rootPath);
            $parentCategory->save();
            $parentId = $parentCategory->getId();
            $parentCategory->setAttributeSetId($parentCategory->getDefaultAttributeSetId());
            $parentCategory->save();
      }
        $childCategory = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($store_id)
            ->addFieldToFilter('name', $sub_category)
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();
        $categoryId = $childCategory->getId();
        if (empty($categoryId))
        {
            $childCategory = Mage::getModel('catalog/category');
            $childCategory->setName($sub_category);
            $childCategory->setIsActive(1);
            $childCategory->setDisplayMode('PRODUCTS');
            $childCategory->setPath($parentCategory->getPath()); 
            $childCategory->save();
            $categoryId = $childCategory->getId();
            $childCategory->setAttributeSetId($childCategory->getDefaultAttributeSetId());
            $childCategory->save();
       }
       return array( $parentId, $categoryId );
    }    
    
    public function getAttributeOptionId( $attributeCode, $newValue )
    {
        /* $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')
            ->getTypeId();
	$attribute = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setCodeFilter($attributeCode)
                ->setEntityTypeFilter($entityTypeID)
                ->getFirstItem();*/
	$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product',$attributeCode);
        if ($attribute == null || $attribute->getId() <= 0) return null;
	$attribute_id = $attribute->getId();
	if ($newValue == '')
        {
	    if ($attribute->getDefaultValue() != '')
            {
	        return $attribute->getDefaultValue();
            }
            else
            {
		return null;
	    }
	}
	$mageAttrOptions = $attribute->getSource()->getAllOptions(false);
	$attrOptions = array();
	foreach ($mageAttrOptions as $option)
        {
	    $attrOptions[$option['label']] = $option['value'];
	}
	if (!isset($attrOptions[$newValue]))
        {
	    $_optionArr = array('value'=>array(), 'order'=>array(), 'delete'=>array());
	    foreach ($attrOptions as $label => $value)
            {
		$_optionArr['value'][$value] = array($label);
	    }	   
	    $_optionArr['value']['option_1'] = array($newValue);
	    $attribute->setOption($_optionArr);
	    $attribute->save();
	}
        //reset the attribute
        /* $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')
            ->getTypeId();
	$attribute = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setCodeFilter($attributeCode)
                ->setEntityTypeFilter($entityTypeID)
                ->getFirstItem();*/
	$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product',$attributeCode);
 	return $attribute->getSource()->getOptionId($newValue);
    }
    
    protected function saveProduct( $product_data )
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $store_id = '0';
        $category = utf8_encode($product_data['INV_PCLASS_DESC2']);
        $sub_category = utf8_encode($product_data['INV_PSCLASS_DESC2']);
        $categoryId = $this->getCategoryId( $store_id, $category, $sub_category );

        switch( $product_data['INV_DEPT_DESC1'] )
        {
            case 'Hommes' :
                $gender = 'man';
                break;
            Default :
                $gender = 'woman';
        }
        $visibility = 1;
        if( empty($product_data['SKU_DIM1']) )
        {
            $visibility = 4;
        }
        $size = ''.$this->getAttributeOptionId( 'size', $product_data['SKU_DIM1'] );
        $taxId = ( $product_data['INV_PCLASS_TAXCLASS'] == 'TX' ) ? 2 : 0;
        $web_enabled = ( $product_data['INV_ISWEBPRODUCT'] == 'Y' ) ? 1 : 0;
        // [INV_BMKS] => M
        // [INV_ELECLASS1] => TAM
        try
        {
            $simpleProduct = Mage::getModel("catalog/product")
                ->loadByAttribute('sku',$product_data['SKU_SKUID']);
            if( !$simpleProduct ) $simpleProduct = Mage::getModel("catalog/product");
            $simpleProduct->setAttributeSetId(4)
                ->setTypeId('simple')
                ->setVisibility($visibility)
                ->setTaxClassId($taxId)
                ->setCreatedAt(strtotime('now'))
                ->setName($product_data['INV_DESC_2'])
                //->setGender($gender)
                ->setSku($product_data['SKU_SKUID'])
                ->setWeight($product_data['INV_WEIGHT'])
                ->setStatus($web_enabled)
                ->setPrice($product_data['PR_RETAILPRICE'])
                ->setCategoryIds($categoryId)
                ->setStoreId($store_id)
                ->setData( 'size', $size )
                ->setData( 'gender', $gender )
                ->setWebsiteIDs(array(1))
                ->setDescription($product_data['INV_DESC_2'])
                ->setShortDescription($product_data['INV_DESC_2'])
                ->setsetupCost($product_data['INV_COST']);
            $simpleProduct->save();
			$store_id = 2; //jj_fr
            $simpleProduct->setName($product_data['INV_DESC_1'])
                ->setStoreId($store_id)
                ->setDescription($product_data['INV_DESC_1'])
                ->setShortDescription($product_data['INV_DESC_1']);
            $simpleProduct->save();
			$store_id = 4; //lpst_fr
            $simpleProduct->setName($product_data['INV_DESC_1'])
                ->setStoreId($store_id)
                ->setDescription($product_data['INV_DESC_1'])
                ->setShortDescription($product_data['INV_DESC_1']);
            $simpleProduct->save();
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateInventory($product_data['SKU_SKUID']) )
            {
                $this->updateInventoryPosition( $simpleProduct );
            }
            if( $visibility == 4 ) return true;
        }
        catch(Exception $e)
        {
            Mage::log('Could not save '.$product_data['SKU_SKUID'].' : '.$e->getMessage());
        }
        try
        {
            $configurableProduct = Mage::getModel("catalog/product")
                ->loadByAttribute('sku',$product_data['INV_PRODUCTCODE']);
            if( !$configurableProduct ) $configurableProduct = Mage::getModel("catalog/product");
            $configurableProduct->setAttributeSetId(4)
                ->setTypeId('configurable')
                ->setVisibility(4)
                ->setTaxClassId($taxId)
                ->setCreatedAt(strtotime('now'))
                ->setName($product_data['INV_DESC_2'])
                //->setGender($gender)
                ->setData( 'super_product', $product_data['INV_DEFSUPPPRODID'] )
                ->setSku($product_data['INV_PRODUCTCODE'])
                ->setWeight($product_data['INV_WEIGHT'])
                ->setStatus($web_enabled)
                ->setPrice($product_data['PR_RETAILPRICE'])
                ->setCategoryIds($categoryId)
                ->setStoreId($store_id)
                ->setData( 'gender', $gender )
                ->setWebsiteIDs(array(1))
                ->setDescription($product_data['INV_DESC_2'])
                ->setShortDescription($product_data['INV_DESC_2'])
                ->setsetupCost($product_data['INV_COST']);
            $configurableProduct->save();
			$store_id = 2; //jj_fr
            $configurableProduct->setName($product_data['INV_DESC_1'])
                ->setStoreId($store_id)
                ->setDescription($product_data['INV_DESC_1'])
                ->setShortDescription($product_data['INV_DESC_1']);
            $configurableProduct->save();
			$store_id = 4; //lpst_fr
            $configurableProduct->setName($product_data['INV_DESC_1'])
                ->setStoreId($store_id)
                ->setDescription($product_data['INV_DESC_1'])
                ->setShortDescription($product_data['INV_DESC_1']);
            $configurableProduct->save();
           //foreach($configAttrCodes as $attrCode)
            //{
            $super_attribute= Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size');
            //}
            if( !Mage::getResourceModel('lesite_erp/productSync')
                ->superAttributeExists( 
                    $configurableProduct->getId(), $super_attribute->getId() 
                ) )
            {
                $configurableAtt = Mage::getModel('catalog/product_type_configurable_attribute')->setProductAttribute($super_attribute);

                $newAttributes[] = array(
                    'id'             => $configurableAtt->getId(),
                    'label'          => $configurableAtt->getLabel(),
                    'position'       => $super_attribute->getPosition(),
                    'values'         => $configurableAtt->getPrices() ? $configurableProduct->getPrices() : array(),
                    'attribute_id'   => $super_attribute->getId(),
                    'attribute_code' => $super_attribute->getAttributeCode(),
                    'frontend_label' => $super_attribute->getFrontend()->getLabel(),
                );
                $configurableProduct->setCanSaveConfigurableAttributes(true);
                $configurableProduct->setConfigurableAttributesData($newAttributes);
            }            

            $configurableProduct->save();

            $ids = $configurableProduct->getTypeInstance()->getUsedProductIds(); 
            $data = array();
            foreach ( $ids as $id )
            {
                $data[$id] = 1;
            }
            $data[$simpleProduct->getId()] = 1;
            $productIds = array_keys($data);
            Mage::getResourceModel('catalog/product_type_configurable')
                ->load($configurableProduct,null)
                ->saveProducts( $configurableProduct, $productIds );

            $stockItem = Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct( $configurableProduct->getId() );
            if (!$stockItem->getId())
            {
                $stockItem->setData( 'product_id', $configurableProduct->getId() );
                $stockItem->setData( 'stock_id', 1 ); 
            }
            $stockItem->setData( 'is_in_stock', 1 ); 
            $stockItem->setData( 'manage_stock', 1 );
            $stockItem->save();
        }
        catch(Exception $e)
        {
            Mage::log('Could not save '.$product_data['INV_PRODUCTCODE'].' : '.$e->getMessage());
        }
    }

    protected function updateInventoryPosition( $product )
    {
        $result = Mage::getResourceModel('lesite_erp/productSync')
            ->getSyncInventoryInfo($product->getSku());
        $qty = $result['qty'];
        $is_in_stock = $qty ? '1' : '0';
        $stock_item = Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct( $product->getId() );
        if (!$stock_item->getId())
        {
            $stock_item->setData( 'product_id', $product->getId() );
            $stock_item->setData( 'stock_id', 1 ); 
        }
        $stock_item->setData( 'qty', $qty ); 
        $stock_item->setData( 'use_config_manage_stock', 0 ); 
        $stock_item->setData( 'is_in_stock', $is_in_stock ); 
        $stock_item->setData( 'manage_stock', 1 );
        $stock_item->save();
    }
}
