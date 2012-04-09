<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_catalog_checkout_default extends Extension_payPerRentals {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'CheckoutSetShippingStatus',
			'CheckoutAddBlockAfterCart',
			'CheckoutShippingMethodsBeforeList'
		), null, $this);
	}
	
	public function cartHasReservation(){
		global $ShoppingCart;
		if (!isset($this->hasReservation)){
			$this->hasReservation = false;
			foreach($ShoppingCart->getProducts() as $cartProduct){
				if ($cartProduct->hasInfo('PackagedProducts')){
					foreach($cartProduct->getInfo('PackagedProducts') as $PackageCartProduct){
						if ($PackageCartProduct->hasInfo('reservationInfo')){
							$this->hasReservation = true;
							break 2;
						}
					}
				}
				elseif ($cartProduct->hasInfo('reservationInfo')){
					$this->hasReservation = true;
					break;
				}
			}
		}
		return $this->hasReservation;
	}

	public function CheckoutAddBlockAfterCart(){
		global $ShoppingCart, $currencies;
		$htmlCheckboxAll = htmlBase::newElement('checkbox')
						   ->setName('insure_all_products')
						   ->setId('insure_all_product');
		
		$htmlButton = htmlBase::newElement('div')
					  ->attr('id','insure_button')
					  ->html('Update')
					  ->setName('insure_button');

		$isAll = false;
		$isRemove = false;
		$insuranceTotal = 0;
		$rows = array();
		$Reservations = array();
		foreach($ShoppingCart->getProducts() as $cartProduct){
			if ($cartProduct->hasInfo('PackagedProducts')){
				foreach($cartProduct->getInfo('PackagedProducts') as $PackageCartProduct){
					if ($PackageCartProduct->hasInfo('reservationInfo')){
						$Reservations[] = $PackageCartProduct;
					}
				}
			}
			elseif ($cartProduct->hasInfo('reservationInfo')) {
				$Reservations[] = $cartProduct;
			}
		}

		foreach($Reservations as $cartProduct){
			$pID = $cartProduct->getIdString();
			$pInfo = $cartProduct->getInfo();

			$payPerRentals = Doctrine_Query::create()
				->select('insurance')
				->from('ProductsPayPerRental')
				->where('products_id = ?', $pID)
				->fetchOne();

			if ($payPerRentals->insurance > 0){
				$insuranceTotal += (float)$payPerRentals->insurance;
				if (!isset($pInfo['reservationInfo']['insurance']) || $pInfo['reservationInfo']['insurance'] == 0){
					$insuranceText = '(Insurance: ' . $currencies->format($payPerRentals->insurance) . ')';
				}
				else {
					$insuranceText = 'Remove Insurance';
					$isRemove = true;
				}

				$htmlCheckbox = htmlBase::newElement('checkbox')
					->setName('insure_product[]')
					->addClass('insure_product')
					->setValue($cartProduct->getIdString());

				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_INSURE_ALL_PRODUCTS_ON_ORDER') == 'False'){
					$rows[] = '<tr>
										<td class="main" valign="top">' . $htmlCheckbox->draw() . '</td>
										<td class="main" valign="top"><b>' . $cartProduct->getName() . '</b></td>
										<td class="main" valign="top"><small>' . $insuranceText . '</small></td>
							</tr>';
				}
				else {
					$isAll = true;
				}
			}
		}

		if ($isAll){
			$insuranceText = '<span id="insuranceTextRemove" style="display:'.(($isRemove == true)? '':'none').'">'.sysLanguage::get('TEXT_REMOVE_INSURANCE_ALL').'</span>'. '<span id="insuranceText"style="display:'.(($isRemove == false)? '':'none').'">'.sysLanguage::get('TEXT_INSURE_ALL').'</span>';

			$rows[] = '<tr>
						<td class="main" valign="top">' . $htmlCheckboxAll->draw() . '</td>
						<td class="main" valign="top" colspan="2"><b>' . $insuranceText .' (Total: ' . $currencies->format($insuranceTotal) . ')</b></td>
					</tr>';
		}

		if (count($rows) >= 1){
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_INSURE_ALL_PRODUCTS_ON_ORDER') == 'False'){
				 $rows[] = '<tr>
								<td class="main" valign="top">' . $htmlCheckboxAll->draw() . '</td>
								<td class="main" valign="top" colspan="2"><b>' . 'Select All' . '</b></td>
						</tr>';
			}
			return '<div class="main"><b>'.sysLanguage::get('TEXT_INSURE_PRODUCTS').'</b></div>' .
				'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;" id="insure_form">' .
					'<table cellpadding="3" cellspacing="0">' .
						'<tr>' .
							'<td><table cellpadding="3" cellspacing="0">' .
								implode('', $rows) .
							'</table></td>' .
						'</tr>' .
					'<tr><td>'.
					$htmlButton->draw() .
					'</td></tr>'.
					'</table>' .
				'</div>';
		}
		return '';
	}

	public function CheckoutSetShippingStatus(){
		global $appExtension, $order, $ShoppingCart, $onePageCheckout;
		if ($this->isEnabled() === false) return;
		
		$reservationProducts = 0;

		if(Session::exists('onlyReservations')){
			$onlyReservations = Session::get('onlyReservations');
		}else{
			$onlyReservations = true;
		}
		//After will be removed so is not need for a check
		//if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_SELECTION') == 'Before'){
			$hasShipping = false;
			foreach($ShoppingCart->getProducts() as $cartProduct){
				if ($cartProduct->hasInfo('PackagedProducts')){
					foreach($cartProduct->getInfo('PackagedProducts') as $PackageCartProduct){
						if ($PackageCartProduct->hasInfo('reservationInfo') === false){
							$onlyReservations = false;
						}else{
							$ResInfo = $PackageCartProduct->getInfo('reservationInfo');
							if (isset($resInfo['shipping'])){
								if ($hasShipping === false){
									$hasShipping = true;
								}
							}
						}
					}
				}
				elseif ($cartProduct->hasInfo('reservationInfo') === false){
					$onlyReservations = false;
				}else{
					$resInfo = $cartProduct->getInfo('reservationInfo');
					if (isset($resInfo['shipping'])){
						if ($hasShipping === false){
							$hasShipping = true;
						}
					}
				}
			}

			if ($hasShipping === false){
				Session::set('shipping', false);
			}

			if ($onlyReservations === true){
				if ($hasShipping === false){
					$onePageCheckout->onePage['info']['shipping'] = false;
					$onePageCheckout->onePage['shippingEnabled'] = false;
				} else {
				}
			}
			Session::set('onlyReservations', $onlyReservations);
		//}
	}

	private function showShippingData($shippingInfo, $productName, &$tableRows){
		global $order, $currencies, $ShoppingCart, $messageStack;
		$Module = OrderShippingModules::getModule($shippingInfo['module'], true);
		if(isset($shippingInfo['module']) && $shippingInfo['module'] == 'upsreservation'){
			$quote = $Module->quote($shippingInfo['id']);
			if (!isset($quote['error'])){
				$pInfo['reservationInfo']['shipping']['cost'] = (float)$quote['methods'][0]['cost'];
			}else{
				$messageStack->addSession('pageStack','Your have changed the shipping address for a reservation product and the new address is not available.','error');
			}
			$ShoppingCart->updateProduct($pID, $pInfo);
		}
		if($Module->getType() == 'Product' && sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_SHIPPING') == 'True'){
			$tableRows[] = array(
				'columns' => array(
					array('text' => '<b>' . $productName . '</b>'),
					array('text' => ' - ' . $shippingInfo['title']),
					array('text' => '(' . $currencies->format($shippingInfo['cost'], true, $order->info['currency'], $order->info['currency_value']) . ')')
				)
			);
		}
	}
	
	public function CheckoutShippingMethodsBeforeList(&$showStoreMethods){
		global $ShoppingCart;

		$tableRows = array();
		$showStoreMethods = false;
		foreach($ShoppingCart->getProducts() as $cartProduct){
			$ProductType = $cartProduct->getProductClass()->getProductTypeClass();
			if ($cartProduct->hasInfo('PackagedProducts')){
				foreach($cartProduct->getData('PackagedProducts') as $PackageCartProduct){
					if ($PackageCartProduct->hasInfo('purchase_type')){
						if ($PackageCartProduct->getInfo('purchase_type') != 'reservation'){
							$showStoreMethods = true;
							continue;
						}

						if ($PackageCartProduct->hasInfo('reservationInfo')){
							$reservationInfo = $PackageCartProduct->getInfo('reservationInfo');

							if (isset($reservationInfo['shipping']) && $reservationInfo['shipping'] !== false){
								$this->showShippingData($reservationInfo['shipping'], $cartProduct->getName() . ' - ' . $PackageCartProduct->getName(), &$tableRows);
							}else{
								$showStoreMethods = true;
							}
						}
					}
				}
			}else{
				if (method_exists($ProductType, 'getPurchaseType')){
					$PurchaseType = $ProductType->getPurchaseType();
					if ($PurchaseType->getCode() != 'reservation'){
						$showStoreMethods = true;
						continue;
					}
				}

				$pInfo = $cartProduct->getInfo();
				$pID = $cartProduct->getIdString();
				$reservationInfo = $pInfo['reservationInfo'];

				if (isset($reservationInfo['shipping']) && $reservationInfo['shipping'] !== false){
					$this->showShippingData($reservationInfo['shipping'], $cartProduct->getName(), &$tableRows);
				}else{
					$showStoreMethods = true;
				}
			}
		}

		if (sizeof($tableRows) > 0){
			$htmlTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0);

			foreach($tableRows as $rowInfo){
				$htmlTable->addBodyRow($rowInfo);
			}
			return $htmlTable->draw() . ($showStoreMethods === true ? '<br />' : '');
		}
	}
}
?>