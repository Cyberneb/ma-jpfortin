<?php
/**
 * Le Site custom slider
 */

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('slider/slider')};
CREATE TABLE {$this->getTable('slider/slider')} (
  `slider_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `status` tinyint(1) NULL DEFAULT '1',
  PRIMARY KEY (`slider_id`),
  KEY `IDX_SLIDER_SLIDER_ID` (`slider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Slider';

-- DROP TABLE IF EXISTS {$this->getTable('slider/slide')};
CREATE TABLE {$this->getTable('slider/slide')} (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',  
  `image_path` varchar(255) NOT NULL default '',  
  `content` TEXT NOT NULL default '',
  `button_label_1` VARCHAR(255) NOT NULL default '',
  `button_category_id_1` INT,
  `button_label_2` VARCHAR(255) NOT NULL default '',
  `button_category_id_2` INT,
  `position` smallint(5) unsigned NOT NULL,
  `status` smallint(6) NOT NULL default '0',
  `slider_id` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`slide_id`),
  KEY `IDX_SLIDER_SLIDE_SLIDE_ID` (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Slide';

    ");

$installer->endSetup(); 