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
        return $result;
    }
    
    public function getLastAccessed()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'SELECT * FROM ' . $table . ' WHERE last_accessed > last_updated '
               . 'ORDER BY last_accessed DESC LIMIT 1';
        $result = $readConnection->fetchAll($query);
        return $result;
    }
   
    public function getDailyUpdate()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $today = date('Y-m-d');
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'SELECT * FROM ' . $table . ' WHERE last_updated IS NOT NULL '
               . '&& last_updated < ' . $today . ' ORDER BY last_updated DESC LIMIT 1';
        $result = $readConnection->fetchAll($query);
        return $result;
    }
    
    public function getNewProducts()
    {
        // use registry to get products by 100
        $result = array();
        try 
        {
            $db = &ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $sql = 'SELECT SKU_SKUID FROM ChainDrive_inventory'; 
            $rs = $db->Execute($sql); 
            if ($rs)
            {
	         while ($row = $rs->FetchRow())
                 {
	             $result[] = $row; 
	         }
            }
        }   
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
        $resource = Mage::getSingleton('core/resource');
        foreach( $result as $value )
        {
            $writeConnection = $resource->getConnection('core_write');    
            $table = $resource->getTableName('lesite_erp/product_sync');
            $query = 'INSERT IGNORE INTO ' . $table . ' ( sku ) VALUES ( :sku )';
            $binds = array( 'sku' => $value['SKU_SKUID'] );
            $writeConnection->query($query, $binds);
        }
    }
    
    public function addNewProduct()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $today = date('Y-m-d');
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'SELECT sku FROM ' . $table . ' WHERE last_updated IS NULL LIMIT 1';
        $result = $readConnection->fetchAll($query);
         $sku = $result[0]['sku'];
        
        if ( empty($sku) ) return false;
        $result = $this->getProductInfo( $sku );
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'UPDATE ' . $table . ' SET configurable = :configurable, '
               . 'last_accessed = :last_accessed, '
               . 'last_updated = :last_updated, data = :data WHERE sku = :sku';
        $now = date('Y-m-d H:i:s');
        $data = serialize($result);
        $binds = array(
            'configurable' => $result['INV_PRODUCTCODE'],
            'last_accessed' => $now,
            'last_updated' => $now,
            'data' => $data,
            'sku' => $result['SKU_SKUID']
        );
        $writeConnection->query($query, $binds);
        return $result;
    }
    
    public function getProductInfo( $sku )
    {
        try 
        {
            $db = &ADONewConnection('firebird');
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
            echo $e->getMessage();
        }
        return $result;
    }
    
    public function getInventoryInfo( $sku )
    {
        try 
        {
            $db = &ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $statement = "SELECT * FROM ChainDrive_Inventory_by_Store WHERE SKU_BRANCHID = ? SKU_SKUID = ?";
            $result = array();
            $params = array();
            $params[] = 'WE';
            $params[] = $sku;
            $sql = $db->Prepare($statement);
            $rs = $db->Execute($sql,$params); 
            if ($rs)
            {
	         while ($row = $rs->FetchRow())
                 {
	             $result['qty'] = $row['SKU_AVAILABLE'] + 0; 
	         }
            }
        }   
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
        if ( $result['qty'] < 0 ) $result['qty'] = 0;
        return $result;
    }
}
