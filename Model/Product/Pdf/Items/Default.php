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
 * Sales Order Creditmemo Pdf default items renderer
 *
 * @category   Mage
 * @package    Delight_Delightserial
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Delight_Delightserial_Model_Product_Pdf_Items_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract {

	public function draw() {
		$order = $this->getOrder();
		$item = $this->getItem();
		$pdf = $this->getPdf();
		$page = $this->getPage();
		$shift = array(0, 10, 0, 0);
		$leftBound = 35;
		$rightBound = 565;

		// draw name
		$this->_setFontRegular();
		$x = $leftBound;
		foreach (Mage::helper('core/string')->str_split($item->getName(), $x, true, true) as $key => $part) {
			$page->drawText($part, $x, $pdf->y - $shift[0], 'UTF-8');
			$shift[0] += 10;
		}

		// draw options
		$options = $this->getItemOptions();
		if (isset($options)) {
			foreach ($options as $option) {
				// draw options label
				$this->_setFontItalic();
				foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), $x, false, true) as $_option) {
					$page->drawText($_option, $x, $pdf->y - $shift[0], 'UTF-8');
					$shift[0] += 10;
				}
				// draw options value
				$this->_setFontRegular();
				foreach (Mage::helper('core/string')->str_split(strip_tags($option['value']), $x, true, true) as $_value) {
					$page->drawText($_value, $x + 5, $pdf->y - $shift[0], 'UTF-8');
					$shift[0] += 10;
				}
			}
		}

		// draw product description
		foreach ($this->_parseDescription() as $description) {
			$page->drawText(strip_tags($description), $x + 5, $pdf->y - $shift[1], 'UTF-8');
			$shift[1] += 10;
		}
		$x += 220;

		// draw SKU
		foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $part) {
			$page->drawText($part, $x, $pdf->y - $shift[2], 'UTF-8');
			$shift[2] += 10;
		}
		$x += 100;

		$font = $this->_setFontBold();

		// draw Serial-Numbers
		$serials = $item->getDelightserialNumbers();
		if ($serials) {
			foreach ($serials as $serial) {
				$page->drawText($serial, $x, $pdf->y - $shift[3], 'UTF-8');
				$shift[3] += 10;
			}
		}

		$pdf->y -= max($shift) + 10;
	}

	public function getItemOptions() {
		$result = array();
		if ($options = $this->getItem()->getProductOptions()) {
			if (isset($options['options'])) {
				$result = array_merge($result, $options['options']);
			}
			if (isset($options['additional_options'])) {
				$result = array_merge($result, $options['additional_options']);
			}
			if (isset($options['attributes_info'])) {
				$result = array_merge($result, $options['attributes_info']);
			}
		}
		return $result;
	}

	public function getSku($item) {
		if ($item->getProductOptionByCode('simple_sku')) {
			return $item->getProductOptionByCode('simple_sku');
		} else {
			return $item->getSku();
		}
	}
}