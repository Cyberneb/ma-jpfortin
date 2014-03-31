<?php

class Lesite_Erp_Model_ProductSync extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('lesite_erp/product_sync');
    }
    
    public function alreadyRunning()
    {
        $last_update = Mage::getResourceModel('lesite_erp/productSync')->getLastUpdateTime();
        if ( (strtotime($last_update) + 60) > time() )
        {
            return true;
        }
        return false;
    }
    
    public function updateAll()
    {
        $a_minute_ago = time() + 60;
        $last_accessed = Mage::getResourceModel('lesite_erp/productSync')->getLastAccessed();
        while( !empty($last_accessed) && time() < $a_minute_ago )
        {
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateSku($last_accessed[0]['sku']) )
            {
                $this->saveProduct( $last_accessed );
                $last_accessed = Mage::getResourceModel('lesite_erp/productSync')
                    ->getLastAccessed();
                continue;
            }
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateInventory($last_accessed[0]['sku']) )
            {
                $simpleProduct = Mage::getModel("catalog/product")
                    ->loadByAttribute('sku',$product_data['SKU_SKUID']);
            }
            sleep(1);
            $last_accessed = Mage::getResourceModel('lesite_erp/productSync')->getLastAccessed();
        }
        $daily_update = Mage::getResourceModel('lesite_erp/productSync')->getDailyUpdate();
        while( !empty($daily_update) && time() < $a_minute_ago  )
        {
            if( Mage::getResourceModel('lesite_erp/productSync')
                ->updateSku($daily_update[0]['sku']) )
            {
                $this->saveProduct( $daily_update );
            }
            sleep(1);
            $daily_update = Mage::getResourceModel('lesite_erp/productSync')->getDailyUpdate();
        }
        $product_data = Mage::getResourceModel('lesite_erp/productSync')->getNewProducts();
        while( !empty($product_data) && time() < $a_minute_ago )
        {
            $product_data = Mage::getResourceModel('lesite_erp/productSync')->getNewProducts();
        }      
        $product_data = Mage::getResourceModel('lesite_erp/productSync')->addNewProduct();
        while( !empty($product_data) && time() < $a_minute_ago )
        {   
            $this->saveProduct( $product_data );
            $product_data = Mage::getResourceModel('lesite_erp/productSync')->addNewProduct();
        }
        if( time() < $a_minute_ago )
        {
            Mage::getSingleton("core/session")->setSmallestSku(0);
            return true;
        }
        return false;
    }
    
    // store id : admin = 0, jpf_en = 1, jpf_fr = 2, lpst_en = 3, lpst_fr = 4    
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
            $rootPath = '1/2';
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
        $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
	$attribute = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setCodeFilter($attributeCode)
                ->setEntityTypeFilter($entityTypeID)
                ->getFirstItem();
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
		
	    $mageAttrOptions = $attribute->getSource()->getAllOptions(false);
	    $attrOptions = array();
	    foreach ($mageAttrOptions as $option)
            {
		$attrOptions[$option['label']] = $option['value'];
	    }
	    return $attrOptions[$newValue];	
	}
 	return $attrOptions[$newValue];
    }
    
    protected function saveProduct( $product_data )
    {
        $store_id = '0';
        $category = $product_data['INV_PCLASS_DESC2'];
        $sub_category = $product_data['INV_PSCLASS_DESC2'];
        $categoryId = $this->getCategoryId( $store_id, $category, $sub_category );

        switch( $product_data['INV_PSCLASS_DESC2'] )
        {
            case 'HOMMES' :
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
        $size = $this->getAttributeOptionId( 'size', $product_data['SKU_DIM1'] );
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
                ->setGender($gender)
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
            $this->updateInventoryPosition( $simpleProduct );
            if( $visibility == 4 ) return true;

            $configurableProduct = Mage::getModel("catalog/product")
                ->loadByAttribute('sku',$product_data['INV_PRODUCTCODE']);
            if( !$configurableProduct ) $configurableProduct = Mage::getModel("catalog/product");
            $configurableProduct->setAttributeSetId(4)
                ->setTypeId('configurable')
                ->setVisibility(4)
                ->setTaxClassId($taxId)
                ->setCreatedAt(strtotime('now'))
                ->setName($product_data['INV_DESC_2'])
                ->setGender($gender)
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

            $ids = $configurableProduct->getTypeInstance()->getUsedProductIds(); 
            $newids = array();
            foreach ( $ids as $id )
            {
                $newids[$id] = 1;
            }
            $loader = Mage::getResourceModel( 'catalog/product_type_configurable' )
                ->load($configurableProduct);
            $newids[$simpleProduct->getId()] = 1;
            $loader->saveProducts( $configurableProduct, array_keys( $newids ) );

        }
        catch(Exception $e)
        {
            print_r($e); 
        }
    }

    protected function updateInventoryPosition( $product )
    {
        $result = Mage::getResourceModel('lesite_erp/productSync')
            ->getInventoryInfo($product->getSku());
        $qty = $result['qty'];
        $stockItem = Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($product->getId());
        if (!$stockItem->getId()) {
           $stockItem->setData('product_id', $product->getId());
           $stockItem->setData('stock_id', 1); 
        }
        if ($stockItem->getQty() != $qty) {
            $stockItem->setData('qty', $qty);
            $stockItem->setData('is_in_stock', $qty ? 1 : 0);                
            $stockItem->save();               
        }         
    }
    
}
