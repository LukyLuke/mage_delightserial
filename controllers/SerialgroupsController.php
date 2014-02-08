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
 * Adminhtml Serialnumber Groups Controller
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_SerialgroupsController extends Mage_Adminhtml_Controller_Action {

	protected function _initGroup() {
		Mage::register('current_group', Mage::getModel('delightserial/serialgroups'));
		$serials = array();
		$groupId = $this->getRequest()->getParam('id');
		if (!is_null($groupId)) {
			Mage::registry('current_group')->load($groupId);
			$available = Mage::getResourceModel('delightserial/numbers_collection')->addFieldToFilter('group_id', $groupId)->load();
			foreach ($available as $number) {
				$serials[] = $number->getData();
			}
		}
		Mage::register('current_numbers', $serials);
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
			->_addBreadcrumb(Mage::helper('delightserial')->__('Serial Groups'), Mage::helper('delightserial')->__('Serial Groups'))
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
			$this->_initGroup();
			$this->_initAction()
				->_addBreadcrumb(Mage::helper('delightserial')->__('New Serial Group'), Mage::helper('adminhtml')->__('New Serial Group'))
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
			$serials = array();
			if ($id) {
				$model->load($id);
				$collection = Mage::getResourceModel('delightserial/serialgroups_collection')->load();
				foreach ($collection as $attribute) {
					$result[] = $attribute->getData();
				}
				$available = Mage::getResourceModel('delightserial/numbers_collection')->addFieldToFilter('group_id', $id)->load();
				foreach ($available as $number) {
					$serials[] = $number->getData();
				}
			}

			Mage::register('current_group', $model);
			Mage::register('groups', $result);
			Mage::register('current_numbers', $serials);

			$this->_initAction()
				->_addBreadcrumb($id ? Mage::helper('delightserial')->__('Edit Serial Group') : Mage::helper('delightserial')->__('New Serial Group'), $id ? Mage::helper('delightserial')->__('Edit Serial Group') : Mage::helper('delightserial')->__('New Serial Group'))
				->_addContent($this->getLayout()->createBlock('delightserial/serialgroups_edit'))
				->renderLayout();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$this->_redirect('*/*/index');
		}
	}

	public function saveAction() {
		$groupModel = Mage::getModel('delightserial/serialgroups');
		$id = $this->getRequest()->getParam('id');
		if (!is_null($id)) {
			$groupModel->load($id);
		}

		try {
			// Save the Group
			$groupModel->setGroupName($this->getRequest()->getParam('group_name'))->save();

			// Get all Submitted Numbers
			$numbers = array();
			foreach (explode("\n", $this->getRequest()->getParam('serials')) as $number) {
				$number = trim($number);
				if (!empty($number)) {
					$purchased = Mage::getModel('delightserial/purchased')->loadBySerial($number);
					if (!$purchased->getSerialNumber()) {
						$numbers[] = trim($number);
					}
				}
			}

			// Delete all no longer used numbers
			if ($groupModel->getId()) {
				$collection = Mage::getResourceModel('delightserial/numbers_collection')->addFieldToFilter('group_id', $groupModel->getId())->load();
				foreach ($collection as $number) {
					if (!in_array($number->getSerialNumber(), $numbers)) {
						$number->delete();
					}
				}
			}

			// Save all new Numbers if they ar not already purchased
			if (is_array($numbers)) {
				$groupId = $groupModel->getId();
				foreach ($numbers as $number) {
					$model = Mage::getModel('delightserial/numbers')->loadBySerial($number);
					if (!$model->getSerialNumber()) {
						Mage::getModel('delightserial/numbers')->setGroupId($groupId)->setSerialNumber($number)->save();
					}
				}
			}

			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('delightserial')->__('Serial-Group was successfully saved'));
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/index');
	}

	public function deleteAction() {
		try {
			$id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('delightserial/serialgroups');
			$model->load($id);
			if ($model->getId()) {
				$model->delete();
			}
			$this->_getSession()->addSuccess($this->__('Serial-Group was deleted'));
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('*/*/index');
	}

	public function loadAttributesAction() {
		try {
			$this->getResponse()->setBody($this->getLayout()
				->createBlock('delightserial/serialgroups_edit_groups')
				->setAttributeSetId($this->getRequest()->getParam('groups_set_id'))
				->setGbaseItemtype($this->getRequest()->getParam('gbase_itemtype'))
				->setAttributeSetSelected(true)
				->toHtml());
		} catch (Exception $e) {
			// just need to output text with error
			$this->_getSession()->addError($e->getMessage());
		}
	}

	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('catalog/delightserial/serialgroups');
	}

}