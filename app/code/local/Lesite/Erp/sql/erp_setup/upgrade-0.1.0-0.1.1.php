<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	ALTER TABLE `erp_product_sync` ADD `locked` INT NOT NULL DEFAULT '0'
	");

$installer->endSetup();
