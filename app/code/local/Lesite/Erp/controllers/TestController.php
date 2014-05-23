<?php

ignore_user_abort(true);
set_time_limit(0);

class Lesite_Erp_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $product_sync = Mage::getModel('lesite_erp/productSync');
        if( $product_sync->alreadyRunning() )
        {
            // unlock
			return false;
        }
        $allProductsUpdated = $product_sync->updateAll();
        while ( !$allProductsUpdated )
        {
            $allProductsUpdated = $product_sync->updateAll();
        }
    }
    
    public function reindexAction()
    {
        $codes = array(
			'1' => 'catalog_product_attribute',
			'2' => 'catalog_product_price',
			'3' => 'catalog_url',
			'4' => 'catalog_product_flat',
			'5' => 'catalog_category_flat',
			'6' => 'catalog_category_product',
			'7' => 'catalogsearch_stock',
			'8' => 'catalog_inventory_stock',
			'9' => 'tag_summary'
			);
		foreach($codes as $key => $code)
		{
			try
			{
				$process = Mage::getSingleton('index/indexer')->getProcessById($key);
				if( $process->getStatus() == Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX )
				{
					$process->reindexAll();
				}
			}
			catch(Exception $e)
			{
				Mage::log( 'Could not reindex: '.$e->getMessage() );
			}
		}
    }
    
    public function salesOrderAction()
    {
        $order_sync = Mage::getModel('lesite_erp/orderSync');
		$order_sync->getOrderInfo();
        $order_sync->syncOrders();    }
    
}