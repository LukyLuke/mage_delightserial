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
 * Adminhtml Serialnumber Groups Form-Widget
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Serialgroups_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();

		$itemType = Mage::registry('current_group');

		$fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->__('Serial-Group')));

		$fieldset->addField('input_serial_group', 'text', array('label' => $this->__('Group Name'), 'title' => $this->__('Serial-Group Name'), 'name' => 'group_name', 'required' => true, 'value' => $itemType->getGroupName()));
		$fieldset->addField('group_id', 'hidden', array('name' => 'id', 'value' => $itemType->getId()));

		//$serialsBlock = $this->getLayout()->createBlock('delightserial/serialgroups_edit_groups');
		$serials = Mage::registry('current_numbers');
		$serialsList = '';
		if (is_array($serials) && count($serials) > 0) {
			foreach ($serials as $serial) {
				$serialsList .= $serial['serial_number'] . chr(10);
			}
		}
		$fieldset->addField('serials', 'textarea', array('label' => $this->__('Serial Numbers'), 'name' => 'serials', 'value' => $serialsList));

		$form->addValues($itemType->getData());
		$form->setUseContainer(true);
		$form->setId('edit_form');
		$form->setMethod('post');
		$form->setAction($this->getSaveUrl());
		$this->setForm($form);
	}

	public function getItemType() {
		return Mage::registry('current_group');
	}

	public function getSaveUrl() {
		return $this->getUrl('*/*/save', array('group_id' => $this->getItemType()->getId()));
	}
}