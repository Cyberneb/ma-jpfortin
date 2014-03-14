<?php
/**
 * Le Site custom slider
 */

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE `slider_slide`  ADD `image_alt` VARCHAR(255) NOT NULL AFTER `image_path`;
            
  CREATE TABLE `slider_slide_store` (
  `slide_id` int(11) UNSIGNED NOT NULL,
  `store_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`slide_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `slider_slide_store` ADD  CONSTRAINT `PK_SLIDER_SLIDE_ID` FOREIGN KEY (`slide_id`) 
REFERENCES `slider`.`slider`(`slider_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `slider_slide_store` ADD  CONSTRAINT `PK_SLIDER_SLIDE_STORE` FOREIGN KEY (`store_id`) 
REFERENCES `slider`.`core_store`(`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    
$installer->endSetup(); 