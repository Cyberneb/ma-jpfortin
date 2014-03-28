<?php

require_once ('adodb5/adodb.inc.php');

class Lesite_Erp_Model_Resource_OrderSync  extends Mage_Catalog_Model_Resource_Abstract
{
    public function exportOrder( $order )
    {
        $orderId = $order->getEntityID();
        $billingAddress = $order->getBillingAddress();
        $address = $billingAddress->getStreet();
        $billingAddress1 = $address[0];
        $billingAddress2 = ''.@$address[1];
        $shippingAddress = $order->getShippingAddress();
        $address = $shippingAddress->getStreet();
        $shippingAddress1 = $address[0];
        $shippingAddress2 = ''.@$address[1];
        $items = $order->getAllItems();
        $lineNum = 1;
        $payment = $order->getPayment();
        $expiry_month = str_pad($payment->getCcExpMonth(),2,'0',STR_PAD_LEFT);
        $expiry_year = str_pad($payment->getCcExpYear(),2,'0',STR_PAD_LEFT);
        
        try
        {
            // get ID from db ?
            $statement = "INSERT INTO Import_Orders ( ID, ORDER_NUMBER1, ORDER_NUMBER2, "
                       . "ORDER_CREATED_AT, IMPORT_STATUS, ORDER_NOTES, IPADDRESS, "
                       . "ORDER_PRODUCT_COST, ORDER_SHIPPING_AMOUNT, ORDER_TAX_COST, "
                       . "ORDER_TAX_COST2, DISCOUNT, CUSTID, FIRST_NAME, LAST_NAME"
                       . "TELEPHONE, ADDRESS1, ADDRESS2, CITY, STATE, ZIP, COUNTRY_ID, "
                       . "EMAIL, S_FIRST_NAME, S_LAST_NAME, S_TELEPHONE, S_ADDRESS1, "
                       . "S_ADDRESS2, S_CITY, S_STATE, S_ZIP, S_COUNTRY_ID ) VALUES( "
                       . "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "
                       . "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
            $params = array();
            $params[] = $orderId;
            $params[] = $order->getIncrementID();
            $params[] = $order->getOriginalIncrementID();
            $params[] = $order->getCreatedAt();
            $params[] = '0'; // = Importing
            $params[] = $order->getCustomerNote();
            $params[] = $order->getRemoteIp();
            $params[] = $order->getBaseSubtotal();
            $params[] = $order->getBaseShippingAmount();
            $params[] = $order->getTaxAmount();
            $params[] = $order->getTaxAmount(); // tax amount 2 ?
            $params[] = $order->getDiscountAmount();
            $params[] = $order->getCustomerID();
            $params[] = $order->getCustomerFirstname();
            $params[] = $order->getCustomerLastname();
            $params[] = $billingAddress->getTelephone();
            $params[] = $billingAddress1;
            $params[] = $billingAddress2;
            $params[] = $billingAddress->getCity();
            $params[] = $billingAddress->getRegion();
            $params[] = $billingAddress->getPostcode();
            $params[] = $billingAddress->getCountryId();
            $params[] = $order->getCustomerEmail();
            $params[] = $shippingAddress->getFirstname();
            $params[] = $shippingAddress->getLastname();
            $params[] = $shippingAddress->getTelephone();
            $params[] = $shippingAddress1;
            $params[] = $shippingAddress2;
            $params[] = $shippingAddress->getCity();
            $params[] = $shippingAddress->getRegion();
            $params[] = $shippingAddress->getPostcode();
            $params[] = $shippingAddress->getCountryId();
            $db = &ADONewConnection('firebird');
            //$db->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $rs = $db->Execute( $statement, $params ); 

            foreach ($items as $item)
            {
                $statement = "INSERT INTO Import_Order_Line_Items ( ID, ORDER_ID, LINENUM, "
                           . "ITEM_ID, QUANTITY, UNIT_PRICE, RETAILPRICE, PKPRICE "
                           . " ) VALUES( ?, ?, ?, ?, ?, ?, ?, ? )";
                $params = array();
                $params[] = null;
                $params[] = $orderId;
                $params[] = $lineNum;
                $params[] = $item->getSku();
                $params[] = intval($item->getQtyOrdered());
                $params[] = number_format($item->getPrice(), 2);
                $params[] = number_format($item->getBasePrice(), 2);
                $params[] = number_format($item->getOriginalPrice(), 2);
                $rs = $db->Execute( $statement, $params ); 
                $lineNum++;
            } 

            $statement = "INSERT INTO Import_Order_Payment ( ID, ORDER_ID, AMOUNT_PAID, "
                       . "CURRENCY, EXCHANGE, CREDCARD, EXPDATE, CARDHOLDER, "
                       . "PAYMENT_TYPE_ID, CREATED_AT, AUTHNUMBER, REFNUMBER, AVS_CODE,"
                       . "ISPREAUTH ) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
            $params = array();
            $params[] = null;
            $params[] = $orderId;
            $params[] = number_format($payment->getAmountPaid(), 2);
            $params[] = $order->getOrderCurrencyCode();
            $params[] = number_format($order->getBaseToOrderRate(), 2);
            $params[] = $payment->getCcLast4();
            $params[] = $expiry_month.$expiry_year;
            $params[] = $payment->getCcOwner();
            $params[] = $payment->getCcType();
            $params[] = $order->getCreatedAt();
            $params[] = $order->getCcTransId();
            $params[] = $order->getCcApproval();
            $params[] = $order->getCcAvsStatus();
            $params[] = 'N';
            $rs = $db->Execute( $statement, $params ); 

            $statement = "UPDATE Import_Orders SET IMPORT_STATUS = ? WHERE ID = ?";
            $params = array();
            $params[] = '1';
            $params[] = $orderId;
            $rs = $db->Execute( $statement, $params ); 
 
            $writeConnection = $resource->getConnection('core_write');    
            $table = $resource->getTableName('lesite_erp/order_sync');
            $query = 'INSERT INTO ' . $table . ' ( order_id, created_at ) '
                   . 'VALUES ( :order_id, :created_at )';
            $binds = array( 
                ':order_id' => $order->getIncrementID(), 
                ':order_id' => date('Y-m-d H:i:s') 
            );
            $writeConnection->query($query, $binds);

        }
        catch ( Exception $e )
        {
            print_r($e);
        }
    }
    
    public function orderExists( $order_id )
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/order_sync');
        $query = 'SELECT order_id FROM ' . $table . " WHERE order_id = '" . $order_id . "'";
        $result = $readConnection->fetchAll($query);
        return $result;
    }
    
    public function getOrders()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/order_sync');
        $query = 'SELECT order_id FROM ' . $table;
        $result = $readConnection->fetchAll($query);
        return $result;
    }
    
    public function updateInfo( $order_id )
    {
        $result = array();
        try 
        {
            $db = &ADONewConnection('firebird');
            $db->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $sql = 'SELECT * FROM ChainDrive_orders WHERE ORDERID = ?'; 
            $params = array();
            $params[] = $order_id;
            $rs = $db->Execute($sql,$params); 
            if ($rs)
            {
	         while ($row = $rs->FetchRow())
                 {
	             $result = $row; 
	         }
            }
            if ( empty($result) ) return $result;
        }   
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
        // do we need to update?
        print_r($result);
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/order_sync');
        $query = 'UPDATE ' . $table . ' SET last_updated = :last_updated, '
               . 'data = :data WHERE order_id = :order_id';
        $now = date('Y-m-d H:i:s');
        $data = serialize($result);
        $binds = array(
            'last_updated' => $now,
            'data' => $data,
            'order_id' => $result['ORDERID']
        );
        $writeConnection->query($query, $binds);
        return $result;
    }
   
    public function remove( $order_id )
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/order_sync');
        $query = 'DELETE FROM ' . $table . ' WHERE order_id = :order_id';
        $binds = array( 'order_id' => $result['ORDERID'] );
        $writeConnection->query($query, $binds);
    }
}
