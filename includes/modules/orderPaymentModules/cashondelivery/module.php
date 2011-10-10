<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrderPaymentCashondelivery extends StandardPaymentModule
{

	public function __construct() {
		global $order;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Cash on Delivery');
		$this->setDescription('Cash on Delivery');

		$this->init('cashondelivery');

		if (is_object($order) && $this->isEnabled() == true){
			if ($order->content_type == 'virtual'){
				$this->enabled = false;
			}
		}
	}

	public function sendPaymentRequest($requestData) {
		return $this->onResponse(array(
				'orderID' => $requestData['orderID'],
				'amount' => $requestData['amount'],
			'message' => sysLanguage::get('PAYMENT_MODULE_COD_AWAITING_PAYMENT'),
				'success' => /*2*/
				1
			));
	}

	public function processPayment($orderID = null, $amount = null){
		global $order;
		if(is_null($orderID) && is_null($amount)){
			return $this->sendPaymentRequest(array(
				'orderID' => $order->newOrder['orderID'],
				'amount'  => $order->info['total']
			));
		}else{
			return $this->sendPaymentRequest(array(
					'orderID' => $orderID,
					'amount'  => $amount
			));
		}
	}

	public function processPaymentCron($orderID) {
		global $order;
		$order->info['payment_method'] = $this->getTitle();

		$this->processPayment();
		return true;
	}

	private function onResponse($logData) {
		$this->onSuccess($logData);
		return true;
	}

	private function onSuccess($logData) {
		$this->logPayment($logData);
	}

	private function onFail($info) {
	}
}

?>