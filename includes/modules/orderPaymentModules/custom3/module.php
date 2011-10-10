<?php
class OrderPaymentCustom3 extends StandardPaymentModule
{

	public function __construct() {
		global $order;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Custom Payment #3');
		$this->setDescription('Custom Payment #3');

		$this->init('custom3');

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
				'message' => 'Awaiting Payment',
				'success' => /*2*/
				1
			));
	}

	public function processPayment() {
		global $order;

		return $this->sendPaymentRequest(array(
				'orderID' => $order->newOrder['orderID'],
				'amount' => $order->info['total']
			));
	}

	public function processPaymentCron($orderID) {
		global $order;
		$order->info['payment_method'] = $this->title;

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