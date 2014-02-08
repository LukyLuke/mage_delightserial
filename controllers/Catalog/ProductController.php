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
 * Catalog Product Controller addition
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController {

	public function preDispatch() {
		parent::preDispatch();
		$this->getRequest()->setRouteName('adminhtml');
	}

	public function delightserialAction() {
		$this->_initProduct();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('delightserial/catalog_product_edit_tab_delightserial')->toHtml()
		);
	}

	protected function _initProduct($block = null) {
		// Strange, but this must be done this way...
		static $product = null;
		if ($block === false) {
			$product = null;
			return null;
		}
		if (!$product) {
			$r = parent::_initProduct();
			if ($block === true) {
				$product = $r;
			}
			return $r;
		}
		return $product;
	}

	protected function _initProductSave() {
		$product = $this->_initProduct(true);
		$groupId = $this->getRequest()->getPost('delightserial_groupid');
		if (!empty($groupId)) {
			$product->setDelightserialData((int)$groupId);
		}
		parent::_initProductSave();
		$this->_initProduct(false);
		return $product;
	}

}
?>