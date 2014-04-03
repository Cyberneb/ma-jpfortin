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
        $query = 'SELECT sku FROM ' . $table . ' WHERE last_accessed > last_updated '
               . 'ORDER BY last_accessed DESC LIMIT 0, 1';
        $result = $readConnection->fetchAll($query);
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
        return $result;
    }
    
    public function getNewProducts()
    {
        $smallest_sku = Mage::getSingleton("core/session")->getSmallestSku();
        if ( empty($smallest_sku) )
        {
            $smallest_sku = 0;
            Mage::getSingleton("core/session")->setSmallestSku($smallest_sku);
        }
        $result = array();
        try 
        {
            $db = &ADONewConnection('firebird');
            //$conn->debug = true;
            $db->Connect('70.25.42.201','WEBADM','WEBADM','C:\multidev\GdbCreation\Web Lab\SV1020_012HO.GBB');
            $db->SetFetchMode(ADODB_FETCH_ASSOC);
            $sql = 'SELECT SKU_SKUID FROM ChainDrive_inventory '
                 . 'WHERE SKU_SKUID > ? ORDER BY SKU_SKUID ASC ROWS 20'; 
            $rs = $db->Execute( $sql, array( $smallest_sku ) ); 
            if ($rs)
            {
	         while ($row = $rs->FetchRow())
                 {
	             $result[] = $row;
                     $smallest_sku = $row['SKU_SKUID'];
 	         }
                 Mage::getSingleton("core/session")->setSmallestSku($smallest_sku);
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
        return $result;
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
        $this->syncProduct( $result );
        return $result;
    }
    
    public function syncProduct( $info )
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'UPDATE ' . $table . ' SET configurable = :configurable, '
               . 'last_accessed = :last_accessed, '
               . 'last_updated = :last_updated, data = :data WHERE sku = :sku';
        $now = date('Y-m-d H:i:s');
        $data = serialize($info);
        $binds = array(
            'configurable' => $info['INV_PRODUCTCODE'],
            'last_accessed' => $now,
            'last_updated' => $now,
            'data' => utf8_encode($data),
            'sku' => $info['SKU_SKUID']
        );
        try {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            print_r( $e );
        }
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
            print_r( $e );
        }
     }
    
    public function updateProductSync( $sku )
    {

       $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/product_sync');
        $query = 'UPDATE ' . $table . ' SET last_updated = :last_updated '
               . 'WHERE sku = :sku';
        $now = date('Y-m-d H:i:s');
        $binds = array( 'last_updated' => $now, 'sku' => $sku );
        try
        {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            print_r( $e );
        }
     }
    
    public function syncInventory( $info )
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');    
        $table = $resource->getTableName('lesite_erp/inventory_sync');
        $query = 'REPLACE INTO ' . $table . ' ( sku, last_accessed, last_updated, '
               . 'qty ) VALUES ( :sku, :last_accessed, :last_updated, :qty )';
        $now = date('Y-m-d H:i:s');
        $binds = array(
            'sku' => $info['sku'],
            'last_accessed' => $now,
            'last_updated' => $now,
            'qty' => $info['qty']            
        );
        try {
            $result = $writeConnection->query($query, $binds);
        } catch ( Exception $e ) {
            print_r( $e );
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
            $this->syncProduct( $chainDriveInfo );
            return true;
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
            print_r( $e );
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
            print_r( $e );
        }
        return $inventory_info;
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
            echo $e->getMessage();
        }
        if ( empty($result['qty']) || $result['qty'] < 0 ) $result['qty'] = 1;
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
            print_r( $e );
        }
        return count($result);
    }
}
