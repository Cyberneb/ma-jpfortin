<?php

class Lesite_Erp_Model_OrderSync extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('lesite_erp/order_sync');
    }
    
    public function alreadyRunning()
    {
        $last_update = Mage::getResourceModel('lesite_erp/orderSync')->getLastUpdateTime();
        if ( (strtotime($last_update) + 60) > time() )
        {
            return true;
        }
        return false;
    }

    public function getOrderInfo( $order_id )
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('status', array('eq' => 'pending'));
        foreach ($orders as $order )
        {
            $orderExists = Mage::getResourceModel('lesite_erp/orderSync')
                ->orderExists( $order->getIncrementID() );
            if ( empty($orderExists) )
            {
                Mage::getResourceModel('lesite_erp/orderSync')->exportOrder( $order );
            }
        }
    }
    
    public function syncOrders()
    {
        $orders = Mage::getResourceModel('lesite_erp/orderSync')->getOrders();
        foreach( $orders as $order )
        {
            $order_info = Mage::getResourceModel('lesite_erp/orderSync')
                ->updateInfo( $order['order_id'] );
            $order_info['INVOICE'] = '10001001';
            if ( !empty($order_info['INVOICE']) )
            {
                $order['tracking'] = $order_info['DEL_SHIPPINGNUMBER'];
                $order['carrier'] = $order_info['TRANSPORT'];
                $shipped = $this->createShipment( $order );
                
            }
            if ( $shipped )
            {
                $order_info = Mage::getResourceModel('lesite_erp/orderSync')
                    ->remove( $order['order_id'] );
                echo 'Shipped!';
            }
        }
    } 
    
    public function createShipment($data)
    {
echo 'Trying to ship';

        try {
            $shipment = false;
            $orderId = $data['order_id'];
            $send_email = 1;
            
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

            if (!$order->getId()) 
            {
                return $this->__('The order no longer exists.');
            }
            
            if ($order->getForcedDoShipmentWithInvoice())
            {
                return $this->__('Cannot do shipment for the order separately from invoice.');
            }
            if (!$order->canShip())
            {
                return $this->__('Cannot do shipment for the order.');
            }
                
            $itemQty = $order->getItemsCollection()->count();
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);

            if ( !empty($order['tracking']) ) 
            {
                $data = array();
                $data['carrier_code'] = 'custom';
                $data['title'] = $order['carrier'];
                $data['number'] = $order['tracking'];
 
                $track = Mage::getModel('sales/order_shipment_track')->addData($data);
                $shipment->addTrack($track);
            }

            if (!$shipment) {
                return false;
            }

            $shipment->register();

            $shipment->getOrder()->setCustomerNoteNotify(true);
            
            $shipment->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();

            if($send_email == 1){
                if(!$shipment->getEmailSent()){
                    $shipment->sendEmail(true);
                    $shipment->setEmailSent(true);
                    $shipment->save();                          
                }
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($shipment, Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
            }
            
            //$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
            //$order->setData('status', Mage_Sales_Model_Order::STATE_COMPLETE);
 
            //$order->save();
            return true;
            
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            return false;
        } 
    }  
}
