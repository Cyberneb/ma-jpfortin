<?php

class Lesite_Erp_Model_Observer
{
    public function syncProductViewed($observer)
    {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        Mage::getResourceModel('lesite_erp/productSync')
            ->accessProduct($product->getSku());
        $socket = fsockopen($_SERVER['HTTP_HOST'],80,$errorno,$errorstr,10);
        if ( $socket )
        {
            $socketdata = "GET /erp/test HTTP 1.1\r\nHost: "
                . $_SERVER['HTTP_HOST'] . "\r\nConnection: Close\r\n\r\n";
            fwrite($socket, $socketdata);
            fclose($socket);
        }
    }

    public function reindex($observer)
    {
		$process = Mage::getModel('index/process')->load('1');
		if( $process->getStatus() == Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX )
		{
			$socket = fsockopen($_SERVER['HTTP_HOST'],80,$errorno,$errorstr,10);
			if ( $socket )
			{
				$socketdata = "GET /erp/test/reindex HTTP 1.1\r\nHost: "
					. $_SERVER['HTTP_HOST'] . "\r\nConnection: Close\r\n\r\n";
				fwrite($socket, $socketdata);
				fclose($socket);
			}
		}
    }
 }

