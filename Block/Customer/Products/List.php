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
 * Block to display SerialNumbers bought by customer
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Block_Customer_Products_List extends Mage_Core_Block_Template {

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();
		$session = Mage::getSingleton('customer/session');
		$purchased = Mage::getResourceModel('delightserial/purchased_collection')
			->addFieldToFilter('customer_id', $session->getCustomerId());
		$this->setPurchased($purchased);
	}

	/**
	 * Enter description here...
	 *
	 * @return Delight_Delightserial_Block_Customer_Products_List
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();

		$pager = $this->getLayout()
			->createBlock('page/html_pager', 'delightserial.customer.products.pager')
			->setCollection($this->getPurchased());
		$this->setChild('pager', $pager);
		$this->getPurchased()->load();
		foreach ($this->getPurchased() as $purchased) {
			$order = Mage::getModel('sales/order')->load($purchased->getOrderId());
			$items = array();
			foreach ($order->getAllVisibleItems() as $item) {
				if ($item->getProductId() == $purchased->getProductId()) {
					$items[] = $item;
				}
			}
			if (count($items) > 0) {
				$purchased->setOrderItem($items);
			}
		}
		return $this;
	}

	/**
	 * Return order view url
	 *
	 * @param integer $orderId
	 * @return string
	 */
	public function getOrderViewUrl($orderId) {
		return $this->getUrl('sales/order/view', array('order_id' => $orderId));
	}

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getBackUrl() {
		return $this->getUrl('customer/account/');
	}

}
