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

/**
 * SerialnumberProduct Collection
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Model_Mysql4_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

	protected function _construct() {
		$this->_init('delightserial/product');
	}

	protected function _initSelect() {
		parent::_initSelect();
		return $this;
	}

	public function loadByGroupId($group) {
		$this->addFieldToFilter('group_id', $group);
		return $this->load();
	}

}