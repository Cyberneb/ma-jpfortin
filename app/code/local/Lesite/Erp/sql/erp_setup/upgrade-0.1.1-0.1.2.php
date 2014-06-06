<?php

$installer = $this;

$table = $installer->getConnection()
	->newTable($installer->getTable('lesite_erp/customer_sync'))
	->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
		'nullable' => false,
		'primary' => true
		), 'Email')
	->addColumn('data', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'nullable' => true,
		'default' => null
		), 'Data')
	->addColumn('last_updated', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
		'nullable' => true,
		'default' => null
		), 'Last Updated')
	->addIndex($installer->getIdxName(
		$installer->getTable('lesite_erp/customer_sync'),
		array('last_updated'),
		Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
		array('last_updated'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->setComment('Customer Sync');

$installer->getConnection()->createTable($table);

?>