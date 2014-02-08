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
 * Serialnumber PDF model
 *
 * @category   Custom
 * @package    Delight_Delightserial
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightserial_Model_Product_Pdf_Serial extends Mage_Sales_Model_Order_Pdf_Abstract {

	const XML_PATH_DELIGHTSERIAL_PDF_PUT_ORDER_ID = 'delightserial_pdf/serialnumber/put_order_id';

	public function getPdf($payments = array()) {
		$this->_beforeGetPdf();
		$this->_initRenderer('delightserial');

		$pdf = new Zend_Pdf();
		$style = new Zend_Pdf_Style();
		$this->_setFontBold($style, 10);

		foreach ($payments as $payment) {
			$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
			$pdf->pages[] = $page;

			$order = $payment->getOrder();

			// Add image
			$this->insertLogo($page, $payment->getStore());

			// Add address
			$this->insertAddress($page, $payment->getStore());

			// Add head
			$this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_DELIGHTSERIAL_PDF_PUT_ORDER_ID, $order->getStoreId()));

			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
			$this->_setFontRegular($page);
			$page->drawText(Mage::helper('delightserial')->__('Serial-Numbers'), 35, 780, 'UTF-8');

			// Add table head
			$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			$page->setLineWidth(0.5);
			$page->drawRectangle(25, $this->y, 570, $this->y - 15);
			$this->y -= 10;
			$page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
			$this->_drawHeader($page);
			$this->y -= 15;

			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

			// Add body
			foreach ($order->getAllItems() as $item) {
				// Add new table head
				if ($this->y < 20) {
					$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
					$pdf->pages[] = $page;
					$this->y = 800;

					$this->_setFontRegular($page);
					$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
					$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
					$page->setLineWidth(0.5);
					$page->drawRectangle(25, $this->y, 570, $this->y - 15);
					$this->y -= 10;
					$this->_drawHeader($page);

					$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
					$this->y -= 20;
				}

				// Load Serial-Numbers if available and not loaded yet
				if (!$item->getDelightserialNumbers()) {
					$serialCollection = Delight_Delightserial_Model_Purchased::getProductSerialCollection($item->getProductId(), $item->getOrderId());
					$serials = array();
					foreach ($serialCollection as $s) {
						$serials[] = $s->getSerialNumber();
					}
					$item->setDelightserialNumbers($serials);
				}

				// Draw item
				$this->_drawItem($item, $page, $order);
			}
		}

		$this->_afterGetPdf();

		return $pdf;
	}

	protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order) {
		$type = $item->getProductType();
		$renderer = $this->_getRenderer($type);
		$renderer->setOrder($order);
		$renderer->setItem($item);
		$renderer->setPdf($this);
		$renderer->setPage($page);

		$renderer->draw();
	}

	protected function _drawHeader(Zend_Pdf_Page $page) {
		$font = $page->getFont();
		$size = $page->getFontSize();

		$page->drawText(Mage::helper('delightserial')->__('Products'), $x = 35, $this->y, 'UTF-8');
		$x += 220;

		$page->drawText(Mage::helper('delightserial')->__('SKU'), $x, $this->y, 'UTF-8');
		$x += 100;

		$text = Mage::helper('delightserial')->__('Serial-Number');
		$page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
		$x += 50;
	}

}
?>