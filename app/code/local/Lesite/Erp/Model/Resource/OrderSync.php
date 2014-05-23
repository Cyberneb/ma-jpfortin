<?php

require_once ('adodb5/adodb.inc.php');

class Lesite_Erp_Model_Resource_OrderSync  extends Mage_Catalog_Model_Resource_Abstract
{
    public function exportOrder( $order )
    {
        $orderId = '100000021';
		//$orderId = $order->getIncrementID();
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
        
        $statement = "INSERT INTO Import_Orders ( ID, ORDER_NUMBER1, ORDER_NUMBER2, "
                       . "ORDER_CREATED_AT, IMPORT_STATUS, ORDER_NOTES, IPADDRESS, "
                       . "ORDER_PRODUCT_COST, ORDER_SHIPPING_COST, ORDER_SHIPPING_TYPE_ID, "
					   . "ORDERTYPE, LOCATION_TYPE, REFERRER, BRANCHID, ORDER_TAX_COST, "
                       . "ORDER_TAX_COST2, DISCOUNT, CUSTID, FIRST_NAME, LAST_NAME, "
                       . "TELEPHONE, ADDRESS1, ADDRESS2, CITY, STATE, ZIP, COUNTRY_ID, "
                       . "EMAIL, S_FIRST_NAME, S_LAST_NAME, S_TELEPHONE, S_ADDRESS1, "
                       . "S_ADDRESS2, S_CITY, S_STATE, S_ZIP, S_COUNTRY_ID ) VALUES( "
                       . "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "
                       . "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
            $params = array();
            $params[] = $orderId;
            $params[] = $orderId;
            $params[] = $orderId;
            $params[] = $order->getCreatedAt();
            $params[] = '0'; // = Importing
            $params[] = ''.$order->getCustomerNote();
            $params[] = ''.$order->getRemoteIp();
            $params[] = $order->getBaseSubtotal();
            $params[] = $order->getBaseShippingAmount();
            $params[] = 'PP';
            $params[] = '13';
            $params[] = '';
            $params[] = '';
            $params[] = 'WE';
            $params[] = $order->getTaxAmount();
            $params[] = $order->getTaxAmount(); // tax amount 2 ?
            $params[] = $order->getDiscountAmount();
            $params[] = $order->getCustomerID();
            $params[] = $order->getCustomerFirstname();
            $params[] = $order->getCustomerLastname();
            $params[] = $billingAddress->getTelephone();
            $params[] = $billingAddress1;
            $params[] = $billingAddress2;
            $params[] = strtoupper( $billingAddress->getCity() );
            $params[] = $billingAddress->getRegionCode();
            $params[] = $billingAddress->getPostcode();
            $params[] = $billingAddress->getCountryId();
            $params[] = $order->getCustomerEmail();
            $params[] = $shippingAddress->getFirstname();
            $params[] = $shippingAddress->getLastname();
            $params[] = $shippingAddress->getTelephone();
            $params[] = $shippingAddress1;
            $params[] = $shippingAddress2;
            $params[] = strtoupper( $shippingAddress->getCity() );
            $params[] = $shippingAddress->getRegionCode();
            $params[] = $shippingAddress->getPostcode();
            $params[] = $shippingAddress->getCountryId();
            $db = ADONewConnection('firebird');
            $db->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $rs = $db->Execute( $statement, $params ); 

            foreach ($items as $item)
            {
                $statement = "INSERT INTO Import_Order_Line_Items ( ID, ORDER_ID, LINENUM, "
                           . "ITEM_ID, QUANTITY, UNIT_PRICE, RETAILPRICE, PKPRICE "
                           . " ) VALUES( ?, ?, ?, ?, ?, ?, ?, ? )";
                $params = array();
                $params[] = $orderId.$lineNum;
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


            $statement = "INSERT INTO Import_Order_Payment ( ID, ORDER_ID, AMOUNT, "
                       . "CURRENCY, EXCHRATE, CREDCARD, EXPDATE, CARDHOLDER, "
                       . "PAYMENT_TYPE_ID, CREATED_AT, AUTHNUMBER, REFNUMBER, AVC_CODE, "
                       . "ISPREAUTH ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
            $params = array();
            $params[] = $orderId;
            $params[] = $orderId;
            $params[] = number_format($payment->getAmountPaid(), 2);
            $params[] = 'CDN'; //$order->getOrderCurrencyCode();
            $params[] = number_format($order->getBaseToOrderRate(), 2);
            $params[] = ''.$payment->getCcLast4();
            $params[] = ''; //$expiry_month.$expiry_year;
            $params[] = ''; //$payment->getCcOwner();
            $params[] = 'V'; //''.$payment->getCcType();
            $params[] = $order->getCreatedAt();
            $params[] = ''.$order->getCcTransId();
            $params[] = ''.$order->getCcApproval();
            $params[] = 'N'; //$order->getCcAvsStatus();
            $params[] = 'N';

            $rs = $db->Execute( $statement, $params ); 

            $statement = "UPDATE Import_Orders SET IMPORT_STATUS = ? WHERE ID = ?";
            $params = array();
            $params[] = '1';
            $params[] = $orderId;
            $rs = $db->Execute( $statement, $params ); 

            $resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');    
            $table = $resource->getTableName('lesite_erp/order_sync');
            $query = 'INSERT INTO ' . $table . ' ( order_id, created_at ) '
                   . 'VALUES ( :order_id, :created_at )';
            $binds = array( 
                'order_id' => $orderId, 
                'created_at' => date('Y-m-d H:i:s') 
            );
            $writeConnection->query($query, $binds);
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

	public function getOrderInfo( $order_id )
	{
		$result = array();
        $db = ADONewConnection('firebird');
        //$db->debug = true;
        $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $sql = 'SELECT * FROM ChainDrive_orders WHERE CUSTPO = ?'; 
        $params = array();
        $params[] = $order_id;
        $rs = $db->Execute($sql,$params); 
        if ($rs)
        {
	        while ($row = $rs->FetchRow())
            {
				$row['ID'] = $row['CUSTPO'];
				$result = $row; 
	        }
        }
		if ( !empty($result) ) return $result;
		$statement = "SELECT * FROM Import_Orders WHERE ID = ?";
		$params = array();
		$params[] = $order_id;
		$rs = $db->Execute( $statement, $params ); 
		if ($rs)
		{
			while ($row = $rs->FetchRow())
			{
				$result = $row; 
			}
		}
		return $result;
	}

    public function getSyncOrderInfo( $order_id )
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/order_sync');
         try
        {
            $query = "SELECT * FROM " . $table . " WHERE order_id = :order_id";
            $binds = array(
                'order_id' => $order_id
            );
            $result = $readConnection->fetchAll($query,$binds);
            $order_info = unserialize(utf8_decode($result[0]['data']));
         } catch ( Exception $e ) {
            Mage::log('Could not getSyncOrderInfo: '.$e->getMessage());
        }
        return $order_info;
    }

	public function syncOrder( $order_info )
	{
		$resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/order_sync');
        $query = 'UPDATE ' . $table . ' SET last_updated = :last_updated, '
               . 'data = :data WHERE order_id = :order_id';
        $now = date('Y-m-d H:i:s');
        $data = serialize($order_info);
        $binds = array(
            'last_updated' => $now,
            'data' => $data,
            'order_id' => $order_info['ID']
        );
        $writeConnection->query($query, $binds);
	}

    public function updateInfo( $order_id )
    {
		$chainDriveInfo = $this->getOrderInfo( $order_id );
        $magentoInfo = $this->getSyncOrderInfo( $order_id );
        if ( $chainDriveInfo !== $magentoInfo )
        {
			if ( @$chainDriveInfo['IMPORT_STATUS'] == '3' )
			{
				$error = $chainDriveInfo['ERRORMESSAGES'];
				Mage::log('There is an error with the updating of the order # '.$order_id.': '.$error);
			}
            $this->syncOrder( $chainDriveInfo );
            return $chainDriveInfo;
        }
        return false;
    }
   
    public function remove( $order_id )
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/order_sync');
        $query = 'DELETE FROM ' . $table . ' WHERE order_id = :order_id';
        $binds = array( 'order_id' => $order_id );
        $writeConnection->query($query, $binds);
    }
}
