<?php

require_once ('adodb5/adodb.inc.php');

class Lesite_Erp_Model_Resource_CustomerSync  extends Mage_Catalog_Model_Resource_Abstract
{
	public function getNewCustomers()
	{
		$fromRow = Mage::getSingleton("core/session")->getFromRow();
		if ( empty($fromRow) )
        {
            $fromRow = 1;
            Mage::getSingleton("core/session")->setFromRow($fromRow);
        }
        $result = array();
        try 
        {
            $db = ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $sql = 'SELECT EMAIL FROM CUSTOMERS WHERE EMAIL != ? '
                 . 'ORDER BY EMAIL ASC ROWS ? TO ?'; 
            $params = array();
            $params[] = '';
            $params[] = $fromRow;
            $params[] = $fromRow + 9999;
            $rs = $db->Execute( $sql, $params ); 
           if ($rs)
            {
				while ($row = $rs->FetchRow())
				{
					if ( filter_var($row['EMAIL'], FILTER_VALIDATE_EMAIL) )
					{
						$result[] = $row;
					}
					
				}
           }
			$fromRow += 10000;
            Mage::getSingleton("core/session")->setFromRow($fromRow);
        }   
        catch (Exception $e)
        {
            Mage::log('Could not getNewCustomers: email missing');
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('lesite_erp/customer_sync');
        foreach( $result as $key => $value )
        {
			$query = 'INSERT IGNORE INTO ' . $table . ' ( email ) VALUES ( :email )';
			$binds = array( 'email' => $value['EMAIL'] );
			$writeConnection->query($query, $binds);
		}
        return $result;
    }
    
    public function addNewCustomer()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $today = date('Y-m-d');
        $table = $resource->getTableName('lesite_erp/customer_sync');
        $query = 'SELECT email FROM ' . $table . ' WHERE last_updated IS NULL LIMIT 0, 1';
        $result = $readConnection->fetchAll($query);
        $email = @$result[0]['email'];
        
        if ( empty($email) ) return array();
        $result = $this->getCustomerInfo( $email );
        $this->syncCustomer( $result );
        return $result;
    }
    
    public function syncCustomer( $info )
    {
        if ( empty($info['EMAIL']) )
        {
            Mage::log('Could not syncCustomert: email missing');
            return false;
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/customer_sync');
        $query = 'UPDATE ' . $table . ' SET last_updated = :last_updated, data = :data WHERE email = :email';
        $now = date('Y-m-d H:i:s');
        $data = serialize($info);
        $binds = array(
            'last_updated' => $now,
            'data' => utf8_encode($data),
            'email' => trim($info['EMAIL'])
        );
        try {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            Mage::log('Could not syncCustomer: '.$e->getMessage());
            return false;
        }
        return true;
     }
    
    public function updateCustomer( $email )
    {
        $chainDriveInfo = $this->getCustomerInfo( $email );
        $magentoInfo = $this->getSyncCustomerInfo( $email );
        if ( $chainDriveInfo !== $magentoInfo )
        {
            return $this->syncCustomer( $chainDriveInfo );
        }
        else
        {
            $this->updateCustomerSync( $email );
        }
        return false;
    }

    public function getSyncCustomerInfo( $email )
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/customer_sync');
         try
        {
            $query = "SELECT * FROM " . $table . " WHERE email = :email";
            $binds = array(
                'email' => $email
            );
            $result = $readConnection->fetchAll($query,$binds);
            $customer_info = unserialize(utf8_decode($result[0]['data']));
         } catch ( Exception $e ) {
            Mage::log('Could not getSyncCustomerInfo: '.$e->getMessage());
        }
        return $customer_info;
    }

    public function getCustomerInfo( $email )
    {
        try 
        {
            $db = ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $statement = 'SELECT * FROM CUSTOMERS WHERE EMAIL = ?';
            $result = array();
            $params = array();
            $params[] = $email;
            $sql = $db->Prepare($statement);
            $rs = $db->Execute($sql,$params); 
            if ($rs)
            {
	         while ($row = $rs->FetchRow())
             {
	             $result = $row; 
	         }
            }
        }   
        catch (Exception $e)
        {
            Mage::log('Could not getCustomerInfo: '.$e->getMessage());
        }
        return $result;
    }

}
