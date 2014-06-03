<?php

require_once ('adodb5/adodb.inc.php');

class Lesite_Erp_Model_Resource_ProductSync  extends Mage_Catalog_Model_Resource_Abstract
{
    public function getLastUpdateTime()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'SELECT MAX( last_updated ) AS last_update FROM ' . $table;
        $result = $readConnection->fetchAll($query);
        return $result[0]['last_update'];
    }
    
	public function getLastAccessed()
	{
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$table = $resource->getTableName('lesite_erp/product_sync');
		$query = 'SELECT sku FROM ' . $table . ' WHERE last_accessed > last_updated '
			. 'ORDER BY last_accessed DESC LIMIT 0, 1';
		$result = $readConnection->fetchAll($query);
		if( !empty($result['sku']) && !$this->lockAccessed($result['sku']) )
		{
			Mage::log('The sync is already running. Stopping the duplicate process.');
			die();
		}
		return $result;
	}

	public function getDailyUpdate()
	{
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$today = date('Y-m-d');
		$table = $resource->getTableName('lesite_erp/product_sync');
		$query = "SELECT sku FROM " . $table . " WHERE last_updated IS NOT NULL "
			. "AND last_updated < '" . $today . "' ORDER BY last_updated DESC LIMIT 0, 1";
		$result = $readConnection->fetchAll($query);
		if( !empty($result['sku']) && !$this->lockAccessed($result['sku']) )
		{
			Mage::log('The sync is already running. Stopping the duplicate process.');
			die();
		}
		return $result;
	}

	public function lockAccessed( $sku )
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('lesite_erp/product_sync');
		$query = "UPDATE " . $table . " SET locked = 1 WHERE sku = '{$sku}' AND locked = 0";
		try
		{
			$result = $writeConnection->exec($query);
		}
		catch ( Exception $e )
		{
			Mage::log('Could not lockAccessed: '.$result.$e->getMessage());
			return false;
		}
		Mage::getSingleton("core/session")->setLockedSku($sku);
		return $result;
	}

	public function unlockAccessed( $sku )
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('lesite_erp/product_sync');
		$query = "UPDATE " . $table . " SET locked = 0 WHERE sku = '{$sku}' AND locked = 1";
		try
		{
			$result = $writeConnection->exec($query);
		}
		catch ( Exception $e )
		{
			Mage::log('Could not unlockAccessed: '.$e->getMessage());
			return false;
		}
		Mage::getSingleton("core/session")->setLockedSku(0);
		return $result;
	}

    public function getNewProducts()
    {
        $smallest_sku = Mage::getSingleton("core/session")->getSmallestSku();
        if ( empty($smallest_sku) )
        {
            $smallest_sku = 1;
            Mage::getSingleton("core/session")->setSmallestSku($smallest_sku);
        }
        $result = array();
        try 
        {
            $db = ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $sql = 'SELECT SKU_SKUID FROM ChainDrive_inventory '
                 . 'ORDER BY SKU_SKUID ASC ROWS ? TO ?'; 
            $params = array();
            $params[] = $smallest_sku;
            $params[] = $smallest_sku + 9999;
            $rs = $db->Execute( $sql, $params ); 
            if ($rs)
            {
				while ($row = $rs->FetchRow())
				{
					$result[] = $row;
				}
           }
			$smallest_sku += 10000;
            Mage::getSingleton("core/session")->setSmallestSku($smallest_sku);
        }   
        catch (Exception $e)
        {
            Mage::log('Could not getNewProducts: sku missing');
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('lesite_erp/product_sync');
        foreach( $result as $value )
        {
            $query = 'INSERT IGNORE INTO ' . $table . ' ( sku ) VALUES ( :sku )';
            $binds = array( 'sku' => $value['SKU_SKUID'] );
            $writeConnection->query($query, $binds);
		}
        return $result;
    }
    
    public function addNewProduct()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $today = date('Y-m-d');
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'SELECT sku FROM ' . $table . ' WHERE last_updated IS NULL LIMIT 0, 1';
        $result = $readConnection->fetchAll($query);
        $sku = @$result[0]['sku'];
        
        if ( empty($sku) ) return false;
		if( !$this->lockAccessed($sku) )
		{
			Mage::log('The sync is already running. Stopping the duplicate process.');
			die();
		}
        $result = $this->getProductInfo( $sku );
        $this->syncProduct( $result );
        return $result;
    }
    
    public function syncProduct( $info )
    {
        if ( empty($info['SKU_SKUID']) )
        {
            Mage::log('Could not syncProduct: sku missing');
            return false;
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'UPDATE ' . $table . ' SET configurable = :configurable, '
               . 'last_accessed = :last_accessed, '
               . 'last_updated = :last_updated, data = :data, locked = :locked WHERE sku = :sku';
        $now = date('Y-m-d H:i:s');
        $data = serialize($info);
        $binds = array(
            'configurable' => @$info['INV_PRODUCTCODE'],
            'last_accessed' => $now,
            'last_updated' => $now,
            'data' => utf8_encode($data),
            'locked' => 0,
            'sku' => trim($info['SKU_SKUID'])
        );
        try {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            Mage::log('Could not syncProduct: '.$e-getMessage());
            return false;
        }
        return true;
     }
    
    public function accessProduct( $sku )
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'UPDATE ' . $table . ' SET last_accessed = :last_accessed '
               . 'WHERE sku = :sku OR configurable = :configurable';
        $now = date('Y-m-d H:i:s');
        $binds = array( 'last_accessed' => $now, 'sku' => $sku, 'configurable' => $sku );
        try
        {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            Mage::log('Could not accessProduct: '.$e->getMessage());
        }
     }
    
    public function updateProductSync( $sku )
    {

       $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'UPDATE ' . $table . ' SET last_updated = :last_updated, locked = :locked '
               . 'WHERE sku = :sku';
        $now = date('Y-m-d H:i:s');
        $binds = array( 'last_updated' => $now, 'locked' => 0, 'sku' => $sku );
        try
        {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            Mage::log('Could not updateProductSync: '.$e->getMessage());
        }
     }
    
    public function syncInventory( $info )
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/inventory_sync');
        $query = 'REPLACE INTO ' . $table . ' ( sku, last_updated, '
               . 'qty ) VALUES ( :sku, :last_updated, :qty )';
        $now = date('Y-m-d H:i:s');
        $binds = array(
            'sku' => $info['sku'],
            'last_updated' => $now,
            'qty' => $info['qty']            
        );
        try {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            Mage::log('Could not syncInventory: '.$e->getMessage());
        }
     }
    
    public function updateInventory( $sku )
    {
        $chainDriveInfo = $this->getInventoryInfo( $sku );
        $magentoInfo = $this->getSyncInventoryInfo( $sku );
        if ( $chainDriveInfo !== $magentoInfo )
        {
            $this->syncInventory( $chainDriveInfo );
            return true;
        }
        return false;
     }
    
    public function updateSku( $sku )
    {
        $chainDriveInfo = $this->getProductInfo( $sku );
        $magentoInfo = $this->getSyncProductInfo( $sku );       
        if ( $chainDriveInfo !== $magentoInfo )
        {
            return $this->syncProduct( $chainDriveInfo );
        }
        else
        {
            $this->updateProductSync( $sku );
        }
        return false;
    }

    public function getSyncProductInfo( $sku )
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/product_sync');
         try
        {
            $query = "SELECT * FROM " . $table . " WHERE sku = :sku";
            $binds = array(
                'sku' => $sku
            );
            $result = $readConnection->fetchAll($query,$binds);
            $product_info = unserialize(utf8_decode($result[0]['data']));
         } catch ( Exception $e ) {
            Mage::log('Could not getSyncProductInfo: '.$e->getMessage());
        }
        return $product_info;
    }

    public function getSyncInventoryInfo( $sku )
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/inventory_sync');
         try
        {
            $query = "SELECT * FROM " . $table . " WHERE sku = :sku";
            $binds = array(
                'sku' => $sku
            );
            $result = $readConnection->fetchAll($query,$binds);
            $inventory_info['qty'] = @$result[0]['qty'] + 0;
            $inventory_info['sku'] = @$result[0]['sku'];
         } catch ( Exception $e ) {
            Mage::log('Could not getSyncInventoryInfo: '.$e->getMessage());
        }
        return $inventory_info;
    }

    public function getProductInfo( $sku )
    {
        try 
        {
            $db = ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $statement = 'SELECT * FROM ChainDrive_inventory WHERE SKU_SKUID = ?';
            $result = array();
            $params = array();
            $params[] = $sku;
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
            Mage::log('Could not getProductInfo: '.$e->getMessage());
        }
        return $result;
    }
    
    public function getInventoryInfo( $sku )
    {
        try 
        {
            $db = ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM',
                'C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $statement = "SELECT * FROM ChainDrive_Inventory_by_Store "
                       . "WHERE SKU_BRANCHID = ? AND SKU_SKUID = ?";
            $result = array();
            $params = array();
            $params[] = '00'; //WE';
            $params[] = $sku;
            $sql = $db->Prepare($statement);
            $rs = $db->Execute($sql,$params); 
            if ($rs)
            {
	         while ($row = $rs->FetchRow())
                 {
	             $result['qty'] = $row['SKU_AVAILABLE']; // test
	         }
            }
        }   
        catch (Exception $e)
        {
            Mage::log('Could not getInventoryInfo: '.$e->getMessage());
        }
        if ( empty($result['qty']) || $result['qty'] < 0 ) $result['qty'] = 3;
		$result['sku'] = $sku; 
        return $result;
    }
    
    public function superAttributeExists( $product_id, $attribute_id )
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        try
        {
            $query = "SELECT * FROM catalog_product_super_attribute WHERE "
                   . "product_id = :product_id AND attribute_id = :attribute_id";
            $binds = array(
                'product_id' => $product_id,
                'attribute_id' => $attribute_id
            );
            $result = $readConnection->fetchAll($query,$binds);
         } catch ( Exception $e ) {
            Mage::log('Could not superAttributeExists: '.$e->getMessage());
        }
        return count($result);
    }
}
