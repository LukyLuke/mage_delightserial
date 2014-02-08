<?php
/**
 * DelightSerial Customisation by delight software gmbh for Magento
 *
 * DISCLAIMER
 *
 * Do not edit or add code to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @copyright  Copyright (c) 2001-2011 delight software gmbh (http://www.delightsoftware.com/)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('delightserial/groups')}`;
    DROP TABLE IF EXISTS `{$installer->getTable('delightserial/purchased')}`;
    DROP TABLE IF EXISTS `{$installer->getTable('delightserial/numbers')}`;
");

$installer->run("
CREATE TABLE `{$installer->getTable('delightserial/groups')}`(
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_name` varchar(250) NOT NULL default '',
  PRIMARY KEY `DELIGHTSERIAL_GROUPS_PK` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
CREATE TABLE `{$installer->getTable('delightserial/purchased')}`(
  `id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `order_id` int(10) unsigned NOT NULL default '0',
  `serial_number` varchar(80) NOT NULL default '',
  PRIMARY KEY `DELIGHTSERIAL_PURCH_PK` (`id`),
  KEY `DELIGHTSERIAL_PURCH_PRODUCT` (`product_id`),
  KEY `DELIGHTSERIAL_PURCH_ORDER` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DELIGHTSERIAL_PURCH_PRODUCT', $installer->getTable('delightserial/purchased'), 'product_id', $installer->getTable('catalog/product'), 'entity_id'
);

$conn->addConstraint(
    'FK_DELIGHTSERIAL_PURCH_ORDER', $installer->getTable('delightserial/purchased'), 'order_id', $installer->getTable('sales/order'), 'entity_id'
);

$installer->run("
CREATE TABLE `{$installer->getTable('delightserial/numbers')}`(
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '0',
  `serial_number` varchar(80) NOT NULL default '',
  PRIMARY KEY `DELIGHTSERIAL_AV_PK` (`id`),
  KEY `DELIGHTSERIAL_AV_GROUP` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DELIGHTSERIAL_AVNUMBERS_GROUP', $installer->getTable('delightserial/numbers'), 'group_id', $installer->getTable('delightserial/groups'), 'id'
);

$installer->endSetup();
