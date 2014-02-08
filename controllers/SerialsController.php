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
 * Adminhtml Serialnumber Controller
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_SerialsController extends Mage_Adminhtml_Controller_Action {

	protected function _initItemType() {
		Mage::register('current_serial', Mage::getModel('delightserial/serialgroups'));
		$typeId = $this->getRequest()->getParam('id');
		if (!is_null($typeId)) {
			Mage::registry('current_serial')->load($typeId);
		}
	}

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog/delightserial/serialgroups')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
			->_addBreadcrumb(Mage::helper('delightserial')->__('Delightserial Base'), Mage::helper('delightserial')->__('Delighsterial Base'));
		return $this;
	}

	public function indexAction() {
		$this->_initAction()
			->_addBreadcrumb(Mage::helper('delightserial')->__('Serial Numbers'), Mage::helper('delightserial')->__('Serial Numbers'))
			->_addContent($this->getLayout()->createBlock('delightserial/serialgroups'))
			->renderLayout();
	}

	/**
	 * Grid for AJAX request
	 */
	public function gridAction() {
		$this->getResponse()->setBody($this->getLayout()->createBlock('delightserial/serialgroups_grid')->toHtml());
	}

	public function newAction() {
		try {
			$this->_initItemType();
			$this->_initAction()
				->_addBreadcrumb(Mage::helper('delightserial')->__('New Serial Number'), Mage::helper('delightserial')->__('New Serial Number'))
				->_addContent($this->getLayout()->createBlock('delightserial/serialgroups_edit'))
				->renderLayout();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$this->_redirect('*/*/index');
		}
	}

	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('delightserial/serialgroups');

		try {
			$result = array();
			if ($id) {
				$model->load($id);
				$collection = Mage::getResourceModel('delightserial/serialgroups_collection')->addAttributeSetFilter($model->getAttributeSetId())->load();
				foreach ($collection as $attribute) {
					$result[] = $attribute->getData();
				}
			}

			Mage::register('current_serial', $model);
			Mage::register('serials', $result);

			$this->_initAction()
				->_addBreadcrumb($id ? Mage::helper('delightserial')->__('Edit Serial Number') : Mage::helper('delightserial')->__('New Serial Number'),
					$id ? Mage::helper('delightserial')->__('Edit Serial Number') : Mage::helper('delightserial')->__('New Serial Number'))
				->_addContent($this->getLayout()->createBlock('delightserial/serials_edit'))
				->renderLayout();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$this->_redirect('*/*/index');
		}
	}

	public function saveAction() {
		$typeModel = Mage::getModel('delightserial/serialgroups');
		$id = $this->getRequest()->getParam('serial_id');
		if (!is_null($id)) {
			$typeModel->load($id);
		}

		try {
			if ($typeModel->getId()) {
				$collection = Mage::getResourceModel('delightserial/serialgroups_collection')->addTypeFilter($typeModel->getId())->load();
				foreach ($collection as $attribute) {
					$attribute->delete();
				}
			}
			$typeModel->setAttributeSetId($this->getRequest()->getParam('serials_set_id'))->setGbaseItemtype($this->getRequest()->getParam('gbase_itemtype'))->save();

			$attributes = $this->getRequest()->getParam('attributes');
			if (is_array($attributes)) {
				$typeId = $typeModel->getId();
				foreach ($attributes as $attrInfo) {
					if (isset($attrInfo['delete']) && $attrInfo['delete'] == 1) {
						continue;
					}
					Mage::getModel('delightserial/serialgroups')->setAttributeId($attrInfo['serial_id'])->setGbaseAttribute($attrInfo['gbase_attribute'])->setTypeId($typeId)->save();
				}
			}

			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('delightserial')->__('Serialnumber was successfully saved'));
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/index');
	}

	public function deleteAction() {
		try {
			$id = $this->getRequest()->getParam('serial_id');
			$model = Mage::getModel('delightserial/serialgroups');
			$model->load($id);
			if ($model->getTypeId()) {
				$model->delete();
			}
			$this->_getSession()->addSuccess($this->__('Item Type was deleted'));
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('*/*/index');
	}

	public function loadAttributesAction() {
		try {
			$this->getResponse()->setBody(
				$this->getLayout()
					->createBlock('delightserial/serialgroups_edit_groups')
					->setAttributeSetId($this->getRequest()->getParam('serials_set_id'))
					->setGbaseItemtype($this->getRequest()->getParam('gbase_itemtype'))
					->setAttributeSetSelected(true)
					->toHtml()
				);
		} catch (Exception $e) {
			// just need to output text with error
			$this->_getSession()->addError($e->getMessage());
		}
	}

	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('catalog/delightserial/serialgroups');
	}
}
?>