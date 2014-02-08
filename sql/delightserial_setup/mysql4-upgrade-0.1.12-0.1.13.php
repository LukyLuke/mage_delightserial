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

$installer->run("ALTER TABLE `{$installer->getTable('delightserial/groups')}` ADD COLUMN ( `informed` tinyint(1) unsigned NOT NULL default 0 );");
$installer->run("UPDATE `{$installer->getTable('delightserial/groups')}` SET `informed`=0;");

$installer->endSetup();
