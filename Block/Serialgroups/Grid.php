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
 * Adminhtml Serialnumber Groups Grid
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Serialgroups_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('groups_grid');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getResourceModel('delightserial/serialgroups_collection');
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	protected function _prepareColumns() {
		$this->addColumn('group_name', array('header' => $this->__('Serial-Group Name'), 'width' => '150px', 'index' => 'group_name'));
		$this->addColumn('items_total', array('header' => $this->__('Total Serial Numbers'), 'width' => '150px', 'index' => 'items_total', 'filter' => false));
		return parent::_prepareColumns();
	}

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current' => true));
	}

}
?>