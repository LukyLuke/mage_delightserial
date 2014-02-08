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
 * Adminhtml Serialnumber Groups Grid-Container
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Serialgroups extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct() {
		$this->_controller = 'serialgroups';
		$this->_blockGroup = 'delightserial';
		$this->_headerText = Mage::helper('delightserial')->__('Serial-Groups');
		$this->_addButtonLabel = Mage::helper('delightserial')->__('Add Group');
		parent::__construct();
	}

}