<?php

class Lesite_Erp_Model_CustomerSync extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('lesite_erp/customer_sync');
    }
    
    /*public function alreadyRunning()
    {
        $last_update = Mage::getResourceModel('lesite_erp/customerSync')
            ->getLastUpdateTime();
        if ( (strtotime($last_update) + 60) > time() )
        {
            return true;
        }
        return false;
    }*/
    
    public function importCustomers()
    {
		Mage::getSingleton("core/session")->setFromRow(0);
        $customer_data = Mage::getResourceModel('lesite_erp/customerSync')
            ->getNewCustomers();
        while( !empty($customer_data) )
        {
            $customer_data = Mage::getResourceModel('lesite_erp/customerSync')
                ->getNewCustomers();
        }
        $customer_data = Mage::getResourceModel('lesite_erp/customerSync')
            ->addNewCustomer();
        while( !empty($customer_data['EMAIL']) )
        { 
            $this->saveCustomer( $customer_data );
            $customer_data = Mage::getResourceModel('lesite_erp/customerSync')
                ->addNewCustomer();
        }
        return false;
    }

    protected function saveCustomer( $customer_data )
    {
		$customer = Mage::getModel('customer/customer');
		$password = $this->random_password(8);
		$email = $customer_data['EMAIL'];
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($email);
		if(!$customer->getId())
		{
			$customer->setEmail($email);
			$customer->setFirstname($customer_data['NAME2']);
			$customer->setLastname($customer_data['NAME1']);
			$customer->setPassword($password);
			try
			{
				$customer->save();
				$customer->setConfirmation(null);
				$customer->save();
				Mage::getSingleton('customer/session')->loginById($customer->getId());
			}
			catch (Exception $e)
			{
				Mage::log('Could not save customer: '.$e->getMessage());
			}
		}
		
		$address = array();
		$address[] = $customer_data['ADDRESS1'];
		if( !empty($customer_data['ADDRESS2']) ) $address[] = $customer_data['ADDRESS2'];
		$regionModel = Mage::getModel('directory/region')->loadByCode($customer_data['STATEPROV'], $customer_data['COUNTRYID']);
		$regionId = $regionModel->getId();
		$_custom_address = array (
			'firstname' => $customer_data['NAME2'],
			'lastname' => $customer_data['NAME1'],
			'street' => $address,
			'city' => $customer_data['CITY'],
			'region_id' => $regionId,
			'region' => $customer_data['STATEPROV'],
			'postcode' => $customer_data['ZIPCODE'],
			'country_id' => $customer_data['COUNTRYID'], 
			'telephone' =>  $customer_data['AREACODE'].'-'.$customer_data['PRIMARYTEL']
		);
		$customAddress = Mage::getModel('customer/address');
		$customAddress->setData($_custom_address)
			->setCustomerId($customer->getId())
			->setIsDefaultBilling('1')
			->setIsDefaultShipping('1')
			->setSaveInAddressBook('1');
		try
		{
			$customAddress->save();
		}
		catch (Exception $e)
		{
			Mage::log('Could not save customer address: '.$e->getMessage());
		}
		Mage::getSingleton('checkout/session')->getQuote()->setBillingAddress(Mage::getSingleton('sales/quote_address')->importCustomerAddress($customAddress));
/*
$customerAddress = Mage::getModel('customer/address');

if ($defaultShippingId = $customer->getDefaultShipping()){
     $customerAddress->load($defaultShippingId); 
} else {   
     $customerAddress
        ->setCustomerId($customer->getId())
        ->setIsDefaultShipping('1')
        ->setSaveInAddressBook('1')
     ;   

     $customer->addAddress($customerAddress);
}            

try {
    $customerAddress
        ->addData($dataShipping)
        ->save()
    ;           
} catch(Exception $e){
    Mage::log('Address Save Error::' . $e->getMessage());
}
*/	}

	protected function random_password( $length = 8 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;
	}

}
