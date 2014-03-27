<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    CREATE TABLE `erp_product_sync` (
    `sku` varchar(20) NOT NULL,
    `configurable` VARCHAR( 20 ) NULL,
    `last_accessed` datetime DEFAULT NULL,
    `last_updated` datetime DEFAULT NULL,
    `data` longtext,
    PRIMARY KEY (`sku`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE  `erp_inventory_sync` (
    `sku` VARCHAR( 20 ) NOT NULL,
    `last_accessed` DATETIME DEFAULT NULL,
    `last_updated` DATETIME DEFAULT NULL,
    `data` `qty` INT( 11 ),
    PRIMARY KEY (  `sku` )
    ) ENGINE = INNODB DEFAULT CHARSET = utf8;

    CREATE TABLE `erp_order_sync` (
    `order_id` varchar(20) NOT NULL,
     `last_accessed` datetime DEFAULT NULL,
    `last_updated` datetime DEFAULT NULL,
    `data` longtext,
    PRIMARY KEY (`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
