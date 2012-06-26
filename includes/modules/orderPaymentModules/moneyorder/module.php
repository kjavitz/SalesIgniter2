<?php
class OrderPaymentMoneyorder extends StandardPaymentModule
{

	public function __construct() {
		global $order;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Check/Money Order');
		$this->setDescription('Check/Money Order');

		$this->init('moneyorder');

		if ($this->isEnabled() === true){
			$this->email_footer = sprintf(
				sysLanguage::get('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER'),
				$this->getConfigData('MODULE_PAYMENT_MONEYORDER_PAYTO'),
				sysConfig::get('STORE_NAME_ADDRESS')
			);
		}
	}

	public function sendPaymentRequest($requestData) {
		return $this->onResponse(array(
				'saleId' => $requestData['saleId'],
				'amount' => $requestData['amount'],
			'message' => sysLanguage::get('PAYMENT_MODULE_MO_AWAITING_PAYMENT'),
				'success' => /*2*/
				1
			));
	}

	public function processPayment(Order $Order){
		return $this->sendPaymentRequest(array(
			'saleId' => $Order->getSaleId(),
			'amount'  => $Order->TotalManager->getTotalValue('total')
		));
	}

	public function processPaymentCron($saleId) {
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