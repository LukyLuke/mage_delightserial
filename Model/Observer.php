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
 * Serials-Observer
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Model_Observer {
	private $_serialsSent;

	/**
	 * Prepare product to save
	 *
	 * @param   Varien_Object $observer
	 * @return  Delight_Delightserial_Model_Observer
	 */
	public function prepareProductSave($observer) {
		$request = $observer->getEvent()->getRequest();
		$product = $observer->getEvent()->getProduct();

		if ($groupId = $request->getPost('delightserial_groupid')) {
			$product->setDelightserialData($groupId);
		}

		return $this;
	}

	/**
	 * Save product
	 *
	 * @param Varian_Object $observer
	 * @return Delight_Delightserial_Model_Observer
	 */
	public function saveProduct($observer) {
		$product = $observer->getEvent()->getProduct();

		if ($product->getDelightserialData()) {
			Mage::getModel('delightserial/product_product')->setEntity($product)->saveProduct();
		} else {
			Mage::getModel('delightserial/product_product')->setEntity($product)->deleteProduct();
		}

		return $this;
	}

	/**
	 * delete a Product
	 *
	 * @param Varien_Object $observer
	 * @return Delight_Delightserial_Model_Observer
	 */
	public function deleteProduct($observer) {
		$product = $observer->getEvent()->getProduct();

		if ($product->getDelightserialData()) {
			Mage::getModel('delightserial/product_product')->setEntity($product)->deleteProduct();
		}

		return $this;
	}

	/**
	 * Set SerialNumbers for Products after a successful payment
	 *
	 * @param Varien_Object $observer
	 * @return Delight_Delightserial_Model_Observer
	 */
	public function setSerialStatus($observer) {
		// @var $order Mage_Sales_Model_Order_Invoice
		$order = $observer->getInvoice()->getOrder();
		$sendSerialEMail = false;

		// We use now "sales_order_payment_pay"-Event, so the Product is paid anyway and we don't have to check the state (which is different by Downladable and others)
		foreach ($order->getAllItems() as $item) {
			$qty = (int)$item->getQtyOrdered();
			for ($i = 0; $i < $qty; $i++) {
				$purchased = Mage::getModel('delightserial/purchased')->setEntity($item)->saveProductOrder($order->getId());
				$sendSerialEMail = ((($i == 0) || $sendSerialEMail) && $purchased->getSerialNeeded() && !$purchased->getWaitingForSerials());
			}
		}

		// Notify the Customer if we have serials on min. one Product with a serial and a serial on each product which needs one (no pending in case of missing serials)
		if ($sendSerialEMail) {
			Mage::getModel('delightserial/email')->sendEmail($order);
		}
		$this->_serialsSent = $sendSerialEMail;
		return $this;
	}

	/**
	 * Check for orders which are waiting for new Serialnumbers and assign them
	 * @param Delight_Delightserial_Model_Observer $observer
	 */
	public function sendPendingSerials($observer) {
		$groupId = $observer->getGroup()->getId();
		$pending = Mage::getResourceModel('delightserial/pending_collection')->addFieldToFilter('group_id', $groupId)->load();

		foreach ($pending as $p) {
			$orderId = $p->getOrderId();
			if (!empty($orderId)) {
				$order = Mage::getModel('sales/order')->loadByAttribute('entity_id', $orderId);
				foreach ($order->getInvoiceCollection() as $invoice) {
					$observer->setInvoice($invoice);
					break;
				}
				$this->setSerialStatus($observer);
				if ($this->_serialsSent) {
					$p->delete();
				}
			}
		}

		// Notify the Customer
		//Mage::getModel('delightserial/email')->sendEmail($order);

		return $this;
	}

	/**
	 * After a Product is loaded, set the Serial-Group as a data-element
	 * @param Delight_Delightserial_Model_Observer $observer
	 */
	public function setProductSerialGroup($observer) {
		$product = $observer->getEvent()->getProduct();
		$serial = Mage::getModel('delightserial/product_product')->loadByProductId($product->getEntityId());
		if ($serial->getId() > 0 && $serial->getGroupId() > 0) {
			$product->setDelightserialData($serial->getGroupId());
		}
		return $this;
	}

}
