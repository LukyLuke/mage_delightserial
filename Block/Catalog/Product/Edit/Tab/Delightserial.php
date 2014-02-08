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
 * Product Edit Tab Form
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Catalog_Product_Edit_Tab_Delightserial
	extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	public function __construct() {
		parent::__construct();
		$this->setProductId($this->getRequest()->getParam('id'));
	}

	protected function _prepareForm() {
		$product = Mage::registry('product');
		$model = Mage::getModel('delightserial/product_product')->load($product->getId(), 'product_id');

		$collection = Mage::getResourceModel('delightserial/serialgroups_collection')->load();
		$options = array(Mage::helper('delightserial')->__('No Serial-Numbers'));
		foreach ($collection as $attribute) {
			$options[$attribute['id']] = $attribute['group_name'];
		}

		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('delightserial_groups', array('legend' => Mage::helper('delightserial')->__('Serial-Number Group')));

		$fieldset->addField('delightserial_groupid', 'select', array(
			'label' => Mage::helper('delightserial')->__('Serial-Group Name'),
			'title' => Mage::helper('delightserial')->__('Serial-Group Name'),
			'name' => 'delightserial_groupid',
			'class' => 'requried-entry',
			'bold' => true,
			'value' => $model->getGroupId(),
			'options' => $options)
		);

		$this->setForm($form);
	}

	public function getTabLabel() {
		return Mage::helper('delightserial')->__('Delight Serial-Numbers');
	}

	public function getTabTitle() {
		return Mage::helper('delightserial')->__('Delight Serial-Numbers');
	}

	public function canShowTab() {
		return true;
	}

	public function isHidden() {
		return false;
	}
}