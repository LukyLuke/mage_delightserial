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
 * SerialNumber-Product model
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Model_Product_Product extends Mage_Core_Model_Abstract
{
	protected $_entity = null;
	protected $_validateEntryFlag = false;

	protected function _construct()
	{
		parent::_construct();
		$this->_init('delightserial/product');
	}

	public function loadByProductId($productId)
	{
		return $this->load($productId, 'product_id');
	}

	public function setEntity(Varien_Object $entity)
	{
		$this->_entity = $entity;
		return $this;
	}

	public function getEntity()
	{
		return $this->_entity;
	}

	public function saveProduct()
	{
		if (is_null($this->getEntity())) {
			return $this;
		}
		if (!$this->getEntity()->getDelightserialData()) {
			return $this;
		}
		$groupId = $this->getEntity()->getDelightserialData();
		$productId = $this->getEntity()->getId();
		$this->load($productId, 'product_id');

		if ($groupId == 0) {
			return $this->deleteProduct();
		}

		$this->setProductId($productId);
		$this->setGroupId($groupId);
		$this->save();
		return $this;
	}

	public function deleteProduct()
	{
		if (is_null($this->getEntity())) {
			return $this;
		}
		$productId = $this->getEntity()->getId();
		$this->load($productId, 'product_id');
		$this->getResource()->delete($this);
		return $this;
	}
}
?>