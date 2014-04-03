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
}

