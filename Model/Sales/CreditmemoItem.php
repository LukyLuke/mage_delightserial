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
//require_once ('app/code/core/Mage/Sales/Model/Order/Creditmemo/Item.php');

/**
 * Order Creditmemo Item model addition
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Model_Sales_CreditmemoItem extends Mage_Sales_Model_Order_Creditmemo_Item {

	// This does not make any sens - a SerialNumber on a CreditMemo - but we load it if there are some
	public function getOrderItem() {
		if (is_null($this->_orderItem)) {
			parent::getOrderItem();
		}
		if (is_null($this->_orderItem)) {
			$this->_orderItem = $this->getCreditmemo()->getItemById($this->getId());
		}
		$item = Mage::getModel('delightserial/purchased')->setEntity($this->_orderItem)->loadByOrder();
		if ($item->getDelightserialNumbers()) {
			$this->setDelightserialNumbers($item->getDelightserialNumbers());
			$this->_orderItem->setDelightserialNumbers($item->getDelightserialNumbers());
		}
		return $this->_orderItem;
	}

}

?>