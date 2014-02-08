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
    DROP TABLE IF EXISTS `{$installer->getTable('delightserial/product')}`;
");

$installer->run("
CREATE TABLE `{$installer->getTable('delightserial/product')}`(
  `id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY `DELIGHTSERIAL_PRODUCT_PK` (`id`),
  KEY `DELIGHTSERIAL_PRODUCT_PRODUCT` (`product_id`),
  KEY `DELIGHTSERIAL_PRODUCT_GROUP` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DELIGHTSERIAL_PRODUCT_PRODUCT', $installer->getTable('delightserial/product'), 'product_id', $installer->getTable('catalog/product'), 'entity_id'
);

$conn->addConstraint(
    'FK_DELIGHTSERIAL_PRODUCT_GROUP', $installer->getTable('delightserial/product'), 'group_id', $installer->getTable('delightserial/groups'), 'id'
);

$installer->endSetup();
