<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

class Lesite_Erp_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $product_sync = Mage::getModel('lesite_erp/productSync');
        if( $product_sync->alreadyRunning() )
        {
            return false;
        }
        $allProductsUpdated = $product_sync->updateAll();
        while ( !$allProductsUpdated )
        {
            $allProductsUpdated = $product_sync->updateAll();
        }
        echo 'Done';
    }
    
    public function salesOrderAction()
    {
        // go through order ids until null
        $order_id = '100000001-1';
        $order_sync = Mage::getModel('lesite_erp/orderSync');
        $order_sync->syncOrders();
    }
    
}