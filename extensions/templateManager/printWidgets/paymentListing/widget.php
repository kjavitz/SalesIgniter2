<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TemplateManagerPrintWidgetPaymentListing extends TemplateManagerPrintWidget
{

	public function __construct() {
		global $App;
		$this->init('paymentListing');
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder) {
		global $currencies;
		$boxWidgetProperties = $this->getWidgetProperties();
		$htmlText = '';
		$Sale = $LayoutBuilder->getVar('Sale');
		$htmlText = $Sale->listPaymentHistory(false)->draw();

		$this->setBoxContent($htmlText);
		return $this->draw();
	}
}

?>