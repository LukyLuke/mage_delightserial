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
 * Purchased Serials model
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Model_Purchased extends Mage_Core_Model_Abstract {
	const XML_PATH_EMAIL_INFORM_ADMINISTRATOR = 'delightserial_email/serialnumber/administrator';

	//const XML_PATH_PURCHASE_STATUS = 'catalog/delightserial/invoice_status';

	protected $_entity = null;

	protected function _construct() {
		$this->_init('delightserial/purchased');
	}

	public function setEntity(Varien_Object $entity) {
		$this->_entity = $entity;
		return $this;
	}

	public function getEntity() {
		return $this->_entity;
	}

	public function saveProductOrder($orderId = null) {
		$this->setSerialNeeded(false);
		$this->setWaitingForSerials(false);

		if (is_null($this->getEntity()) || is_null($orderId)) {
			return $this;
		}

		$serial = null;
		$productId = $this->getEntity()->getProductId();
		$serialProduct = Mage::getModel('delightserial/product_product')->loadByProductId($productId);

		// Check if there isn't already attached a serial-number
		$assigned = 0;
		$collection = $this->getProductSerialCollection($productId, $orderId);
		foreach ($collection as $c) {
			$assigned += strlen($c->getSerialNumber()) > 0 ? 1 : 0;
		}
		$numShip = $this->getEntity()->getQtyToShip();
		if (is_null($numShip) || ($assigned >= $numShip)) {
			return $this;
		}

		if ($serialProduct->getGroupId()) {
			$this->setSerialNeeded(true);
			$numbers = Mage::getModel('delightserial/numbers')->loadByGroupId($serialProduct->getGroupId());

			if ($numbers->getSerialNumber()) {
				// If a SerialNumber is available, get it and store it on the Order-Item
				$serial = $numbers->getSerialNumber();
				$numbers->delete();
			} else {
				// If no Serial-Number is available, set the whole order on the "delightserial/pending" list which is
				// readen after new serials are added
				$pending = Mage::getModel('delightserial/pending')->loadByOrderId($orderId);
				if ($pending->getOrderId() != $orderId) {
					$pending->setOrderId($orderId);
					$pending->setGroupId($serialProduct->getGroupId());
					$pending->save();
				}
				$this->setWaitingForSerials(true);
			}

			$this->checkNumberOfSerials($serialProduct->getGroupId());
		}

		if (is_null($serial)) {
			return $this;
		}

		$this->setOrderId($orderId);
		$this->setProductId($productId);
		$this->setSerialNumber($serial);
		$this->setCustomerId($this->getEntity()->getOrder()->getCustomerId());
		$this->save();
		return $this;
	}

	public function checkNumberOfSerials($groupId) {
		$min = (int)Mage::getStoreConfig('catalog/delightserial/inform_min_numbers');
		$num = (int)Mage::getResourceModel('delightserial/numbers_collection')->loadByGroupId($groupId)->count();
		$group = Mage::getModel('delightserial/serialgroups')->load($groupId);
		//$group->setInformed(0); // DEBUG
		if (($min >= $num) && !$group->isInformed()) {
			$group->setInformed(1);
			$group->save();
			$this->informAdministrator($group, $num);
		}
	}

	protected function informAdministrator(Delight_Delightserial_Model_Serialgroups $group, $numAvailable) {
		$storeId = Mage::app()->getStore()->getStoreId();
		$min = (int)Mage::getStoreConfig('catalog/delightserial/inform_min_numbers', $storeId);
		$email = Mage::getStoreConfig('catalog/delightserial/send_information_to', $storeId);
		$template = Mage::getStoreConfig(self::XML_PATH_EMAIL_INFORM_ADMINISTRATOR, $storeId);
		$emailTemplate = Mage::getModel('core/email_template')->setDesignConfig(array('area','frontend'))
			->sendTransactional(
				$template,
				$email,
				Mage::getStoreConfig('trans_email/ident_'.$email.'/email', $storeId),
				Mage::getStoreConfig('trans_email/ident_'.$email.'/name', $storeId),
				array(
					'group'=>$group,
					'limit'=>$min,
					'numSerials'=>$numAvailable
				)
			);
	}

	public static function getProductSerialCollection($productId, $orderId) {
		return Mage::getResourceModel('delightserial/purchased_collection')
			->addFieldToFilter('product_id', $productId)
			->addFieldToFilter('order_id', $orderId);
	}

	public function loadBySerial($serial) {
		return $this->load($serial, 'serial_number');
	}

	public function loadByOrder() {
		$collection = $this->getProductSerialCollection($this->_entity->getProductId(), $this->_entity->getOrderId())->load()->getItems();
		$serials = array();
		foreach ($collection as $s) {
			$serials[] = $s->getSerialNumber();
		}
		$this->setDelightserialNumbers($serials);
		return $this;
	}

}