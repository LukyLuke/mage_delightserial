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
    DROP TABLE IF EXISTS `{$installer->getTable('delightserial/pending')}`;
");

$installer->run("
CREATE TABLE `{$installer->getTable('delightserial/pending')}`(
  `id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY `DELIGHTSERIAL_PENDING_PK` (`id`),
  KEY `DELIGHTSERIAL_PURCH_ORDER` (`order_id`),
  KEY `DELIGHTSERIAL_PENDING_GROUP` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DELIGHTSERIAL_PENDING_ORDER', $installer->getTable('delightserial/pending'), 'order_id', $installer->getTable('sales/order'), 'entity_id'
);

$conn->addConstraint(
    'FK_DELIGHTSERIAL_PENDING_GROUP', $installer->getTable('delightserial/pending'), 'group_id', $installer->getTable('delightserial/groups'), 'id'
);

$installer->endSetup();
