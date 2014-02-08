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
 * Delightserial EMail
 *
 * @category   Delight
 * @package    Delight_Delightserial
 * @author     delight software gmbh <l.zurschmiede@delightsoftware.com>
 */
class Delight_Delightserial_Model_Email extends Varien_Object {

	const XML_PATH_EMAIL_TEMPLATE       = 'sales_email/delightserial/template';
	const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/delightserial/guest_template';
	const XML_PATH_EMAIL_IDENTITY       = 'sales_email/delightserial/identity';
	const XML_PATH_EMAIL_COPY_TO        = 'sales_email/delightserial/copy_to';
	const XML_PATH_EMAIL_COPY_METHOD    = 'sales_email/delightserial/copy_method';
	const XML_PATH_EMAIL_ENABLED        = 'sales_email/delightserial/enabled';

	/**
	 * Send an Serial-EMail
	 *
	 * @param Varien_Object $observer
	 * @return Mage_Sales_Model_Order_Invoice
	 */
	public function sendEmail($order) {
		// Abort if we don't want to send Serials EMails
		if (!Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED, $order->getStoreId())) {
			return $this;
		}
		$orderId = $order->getStore()->getId();

		$currentDesign = Mage::getDesign()
			->setAllGetOld(array(
				'package' => Mage::getStoreConfig('design/package/name', $orderId),
				'store' => $order->getStoreId()
			));

		// @var $translate Mage_Core_Model_Translate
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);

		$paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())->setIsSecureMode(true);
		$paymentBlock->getMethod()->setStore($orderId);

		$data = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_TO, $orderId);
		$copyTo = false;
		if (!empty($data)) {
			$copyTo = explode(',', $data);
		}
		$copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $orderId);

		if ($order->getCustomerIsGuest()) {
			$template = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $orderId);
			$customerName = $order->getBillingAddress()->getName();
		} else {
			$template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $orderId);
			$customerName = $order->getCustomerName();
		}

		// Initialize the email
		$mailTemplate = Mage::getModel('core/email_template');
		$sendTo[] = array('name' => $customerName, 'email' => $order->getCustomerEmail());

		// Attach the serials-PDF
		$pdf = Mage::getModel('delightserial/product_pdf_serial')->getPdf(array($order->getPayment()));
		$attachment = $mailTemplate->getMail()->createAttachment($pdf->render());
		$attachment->type = 'application/pdf';
		$attachment->filename = 'SerialNumbers.pdf';

		// Check for additional recipients
		if (is_array($copyTo) && ($copyMethod == 'bcc')) {
			foreach ($copyTo as $email) {
				$mailTemplate->addBcc($email);
			}
		} else if (is_array($copyTo) && ($copyMethod == 'copy')) {
			foreach ($copyTo as $email) {
				$sendTo[] = array('name' => null, 'email' => $email);
			}
		}

		// Create the HTML Serials-List
		$serials_html = '<pre>';
		$purchased = Mage::getResourceModel('delightserial/purchased_collection')
			->addFieldToFilter('order_id', $order->getId());
		foreach ($purchased as $serial) {
			$product = Mage::getModel('catalog/product')
				->setStoreId($orderId)
				->load($serial->getData('product_id'));
			$serials_html .= $product->getSku().', '.$product->getName().':'.chr(10);
			$serials_html .= chr(9).$serial->getData('serial_number').chr(10);
		}
		$serials_html .= '</pre>';

		foreach ($sendTo as $recipient) {
			$mailTemplate->setDesignConfig(
				array(
					'area' => 'frontend',
					'store' => $orderId)
				)->sendTransactional(
					$template,
					Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $orderId),
					$recipient['email'],
					$recipient['name'],
					array(
						'order' => $order,
						'billing' => $order->getBillingAddress(),
						'payment_html' => $paymentBlock->toHtml(),
						'serials_html' => $serials_html
					)
				);
		}

		$translate->setTranslateInline(true);

		return $this;
	}
}