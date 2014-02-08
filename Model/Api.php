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
 * Delightserial api
 *
 * @category   Delight
 * @package    Delight_Delightserial
 * @author     delight software gmbh <l.zurschmiede@delightsoftware.com>
 */
class Delight_Delightserial_Model_Api extends Mage_Catalog_Model_Api_Resource
{

	public function setSerialGroup($groups = null) {
		$result = array();
		if (is_array($groups)) {
			foreach ($groups as $group) {
				$_group = Mage::getModel('delightserial/serialgroups');
				if (property_exists($group, 'entity_id')) {
					$_group->load($group->entity_id);
				} else if (property_exists($group, 'name')) {
					$_group->load($group->name, 'group_name');
				}

				// The name is required if the group is not loaded
				if (($_group->getId() <= 0) && !property_exists($group, 'name')) {
					$result[] = (object)array(
						'success' => false,
						'name' => property_exists($group, 'name') ? $group->name : 'unknown',
						'entity_id' => property_exists($group, 'entity_id') ? $group->entity_id : 0
					);
					continue;
				}

				if (property_exists($group, 'name')) {
					$_group->setGroupName($group->name)->save();
				}
				$group->serials = property_exists($group, 'serials') && is_array($group->serials) ? $group->serials : array();
				$group->products = property_exists($group, 'products') && is_array($group->products) ? $group->products : array();

				// response-Object
				$back = (object)array(
					'success' => true,
					'name' => $_group->getGroupName(),
					'entity_id' => $_group->getId()
				);

				// Don't insert already saled or existent serialnumbers
				foreach ($group->serials as $serial) {
					if (!Mage::getModel('delightserial/purchased')->loadBySerial($serial)->getSerialNumber()
					    && !Mage::getModel('delightserial/numbers')->loadBySerial($serial)->getSerialNumber()) {
						Mage::getModel('delightserial/numbers')->setGroupId($_group->getId())->setSerialNumber($serial)->save();
					}
				}

				// apply all products to the group
				$applied = array();
				foreach ($group->products as $product) {
					$prod = null;
					if (property_exists($product, 'entity_id')) {
						$prod = Mage::getModel('catalog/product')->load($product->entity_id);
					} else if (property_exists($product, 'sku')) {
						$prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $product->sku);
					}
					if (($prod instanceof Varien_Object) && $prod->getEntityId() > 0) {
						$prod->setDelightserialData($_group->getId())->save();
						$applied[] = $prod->getEntityId();
						Mage::getModel('delightserial/product_product')->setEntity($prod)->saveProduct();
					}
				}

				// Remove all products from the current group if it is not in the list anymore
				$products = Mage::getResourceModel('delightserial/product_collection')->loadByGroupId($_group->getId());
				foreach ($products as $product) {
					if (!in_array($product->getProductId(), $applied)) {
						$product->delete();
					}
				}

				// Apply all Products if called to the response
				if (property_exists($group, 'return_products') && $group->return_products) {
					$back->products = $this->_getAssignedProducts($_group->getId());
				}
				$result[] = $back;
			}
		}
		return $result;
	}

	public function getSerialGroups($store = null) {
		$result = array();
		$groups = Mage::getResourceModel('delightserial/serialgroups_collection');
		if (!empty($store)) {
			$groups->addFieldToFilter('store_id', $store);
		}
		$groups->load();

		foreach ($groups as $group) {
			$obj = new Varien_Object();
			$obj->setData('entity_id', $group->getId());
			$obj->setData('name', $group->getGroupName());
			$obj->setData('num_serials', $group->getNumSerials());
			$obj->setData('products', $this->_getAssignedProducts($group->getId()));
			$result[] = $obj;
		}

		return $result;
	}

	public function deleteSerialGroup($group) {
		$result = false;
		$_group = null;
		if (property_exists($group, 'entity_id')) {
			$_group = Mage::getModel('delightserial/serialgroups')->load($group->entity_id);
		} else if (property_exists($group, 'name')) {
			$_group = Mage::getModel('delightserial/serialgroups')->load($group->name, 'group_name');
		}
		if (!($_group instanceof Varien_Object) || ($_group->getId() <= 0)) {
			throw new Varien_Exception('No "entity_id" or "name" given on the "delightserialSerialGroupEntity"-Object while calling "delightserialDeleteSerialGroup"');
		}

		$products = Mage::getResourceModel('delightserial/product_collection')->loadByGroupId($_group->getId());
		foreach ($products as $product) {
			Mage::getModel('catalog/product')->load($product->getProductId())->setDelightserialData(0)->save();
			$product->delete();
		}
		$serials = Mage::getResourceModel('delightserial/numbers_collection')->loadByGroupId($_group->getId());
		foreach ($serials as $serial) {
			$serial->delete();
		}
		$_group->delete();
		$result = true;

		return $result;
	}

	protected function _getAssignedProducts($group) {
		$result = array();
		$products = Mage::getResourceModel('delightserial/product_collection')->loadByGroupId($group);
		foreach ($products as $id) {
			$product = Mage::getModel('catalog/product')->load($id->getProductId());
			$obj = new Varien_Object(array(
				'name' => $product->getName(),
				'sku' => $product->getSku(),
				'entity_id' => $product->getEntityId(),
				'websites' => $product->getWebsiteIds()
			));
			$result[] = $obj;
		}
		return $result;
	}
}
