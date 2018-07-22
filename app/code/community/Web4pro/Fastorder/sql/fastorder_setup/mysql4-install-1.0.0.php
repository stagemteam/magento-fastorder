<?php
/**
 * WEB4PRO - Creating profitable online stores
 *
 *
 * @author    WEB4PRO <amutylo@web4pro.com.ua>
 * @category  WEB4PRO
 * @package   Web4pro_Fastorder
 * @copyright Copyright (c) 2014 WEB4PRO (http://www.web4pro.net)
 * @license   http://www.web4pro.net/license.txt
 */

$installer = $this;

$installer->startSetup();

    $installer->run("
    DROP TABLE IF EXISTS  `{$installer->getTable('web4pro_fastorder/country')}`;
    CREATE TABLE `{$installer->getTable('web4pro_fastorder/country')}` (
       `entity_id` int(10) unsigned NOT NULL auto_increment,
       `phone_code` varchar(9) NOT NULL default '',
       `country_code` varchar(9) NOT NULL default '',
       `order` tinyint(4) default '0',
       PRIMARY KEY  (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`, `order`)
        VALUES ('1', 'US', 1);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('44', 'GB', 2);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('1', 'CA', 3);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('49', 'DE', 4);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('33', 'FR', 5);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('48', 'PL', 6);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('41', 'CH', 7);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('420', 'CZ', 8);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('421', 'CK', 9);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('34', 'ES', 10);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('351', 'PT', 11);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`,`order`)
        VALUES ('380', 'UA', 12);
    INSERT INTO  `{$installer->getTable('web4pro_fastorder/country')}` (`phone_code`, `country_code`, `order`)
        VALUES ('7', 'RU', 13);

    DROP TABLE IF EXISTS  `{$installer->getTable('web4pro_fastorder/order')}`;
    CREATE TABLE `{$installer->getTable('web4pro_fastorder/order')}` (
       `entity_id` int(10) unsigned NOT NULL auto_increment,
       `magento_order_id` int(10) unsigned,
       `customer_id` int(10) unsigned,
       `customer_email` varchar(100) NOT NULL default '',
       `quote_id` int(10) unsigned,
       `store_id` int(10) unsigned  NOT NULL,
       `phone` varchar(40) NOT NULL default '',
       `country` varchar(4) NOT NULL default '',
       `comment` text,
       `create_date` datetime NOT NULL,
       PRIMARY KEY  (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
