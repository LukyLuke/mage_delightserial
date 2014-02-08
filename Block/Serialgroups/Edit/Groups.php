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
 * Adminhtml Serialnumber Groups Fieldset-Renderer
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Serialgroups_Edit_Groups extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element {

	protected function _prepareLayout() {
		$this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
			'label' => Mage::helper('delightserial')->__('Add new Serial-Group'),
			'class' => 'add',
			'id' => 'add_new_group',
			'on_click' => 'gBaseAttribute.add()'
		)));
		$this->setChild('delete_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
			'label' => Mage::helper('delightserial')->__('Remove'),
			'class' => 'delete delete-product-option',
			'on_click' => 'gBaseAttribute.remove(event)'
		)));

		parent::_prepareLayout();
	}

	public function getFieldId() {
		return 'delightserial_groups';
	}

	public function getFieldName() {
		return 'groups';
	}

	protected function _toJson($data) {
		return Zend_Json::encode($data);
	}

}