<?php
//print_r($_GET);
//print_r($_POST);
if (isset($_GET['type']) && $_GET['type'] == 'addressBook'){
	switch($_POST['address_type']){
		case 'billing':
			$sessVar = 'billing';
			break;
		case 'shipping':
			$sessVar = 'delivery';
			$_POST['shipping_diff'] = true;
			break;
		case 'pickup':
			$sessVar = 'pickup';
			$_POST['pickup_diff'] = true;
			break;
	}

	$onePageCheckout->onePage[$sessVar . 'AddressId'] = $_POST['address'];

	$address = $userAccount->plugins['addressBook']->getAddress($_POST['address']);
	$userAccount->plugins['addressBook']->addAddressEntry($sessVar, $address);
	$loadPage = 'addresses.php';
}
else {
	$error = false;
	if ($_POST['currentPage'] == 'addresses'){

		$accountValidation = array(
			'entry_firstname'	  => $_POST['billing_firstname'],
			'entry_lastname'	   => $_POST['billing_lastname'],
			'entry_street_address' => $_POST['billing_street_address'],
			'entry_postcode'	   => $_POST['billing_postcode'],
			'entry_city'		   => $_POST['billing_city'],
			'entry_country_id'	 => $_POST['billing_country'],
		);

		if (sysConfig::get('TERMS_CONDITIONS_CHECKOUT') !== 'false'){
			if (array_key_exists('terms', $_POST)){
				$accountValidation['terms'] = $_POST['terms'];
			}
			else {
				$accountValidation['terms'] = 0;
			}
		}
		if (array_key_exists('billing_suburb', $_POST)){
			$accountValidation['entry_suburb'] = $_POST['billing_suburb'];
		}
		if (array_key_exists('billing_state', $_POST)){
			$accountValidation['entry_state'] = $_POST['billing_state'];
		}
		if (array_key_exists('billing_gender', $_POST)){
			$accountValidation['entry_gender'] = $_POST['billing_gender'];
		}
		if (array_key_exists('billing_dob', $_POST)){
			$accountValidation['entry_dob'] = $_POST['billing_dob'];
		}
		if (array_key_exists('billing_fiscal_code', $_POST)){
			$accountValidation['entry_cif'] = $_POST['billing_fiscal_code'];
		}
		if (array_key_exists('billing_vat_number', $_POST)){
			$accountValidation['entry_vat'] = $_POST['billing_vat_number'];
		}
		if (array_key_exists('billing_city_birth', $_POST)){
			$accountValidation['entry_city_birth'] = $_POST['billing_city_birth'];
		}
		if (array_key_exists('billing_company', $_POST)){
			$accountValidation['entry_company'] = $_POST['billing_company'];
		}
		if (array_key_exists('billing_telephone', $_POST)){
			$accountValidation['telephone'] = $_POST['billing_telephone'];
		}
		if (array_key_exists('billing_fax', $_POST)){
			$accountValidation['fax'] = $_POST['billing_fax'];
		}
		if ($userAccount->isLoggedIn() === false){
			if (array_key_exists('billing_email_address', $_POST)){
				$accountValidation['email_address'] = $_POST['billing_email_address'];
			}
		}
		else {
			$accountValidation['email_address'] = $userAccount->getEmailAddress();
		}
		$error = $userAccount->validate($accountValidation);

		$billingAddressArray = array(
			'entry_gender'		 => (isset($_POST['billing_gender']) ? $_POST['billing_gender'] : 'm'),
			'entry_dob'			=> (isset($_POST['billing_dob']) ? $_POST['billing_dob'] : ''),
			'entry_company'		=> (isset($_POST['billing_company']) ? $_POST['billing_company'] : ''),
			'entry_firstname'	  => $_POST['billing_firstname'],
			'entry_lastname'	   => $_POST['billing_lastname'],
			'entry_street_address' => $_POST['billing_street_address'],
			'entry_suburb'		 => (isset($_POST['billing_suburb']) ? $_POST['billing_suburb'] : ''),
			'entry_postcode'	   => $_POST['billing_postcode'],
			'entry_city'		   => $_POST['billing_city'],
			'entry_cif'			=> (isset($_POST['billing_fiscal_code']) ? $_POST['billing_fiscal_code'] : ''),
			'entry_vat'			=> (isset($_POST['billing_vat_number']) ? $_POST['billing_vat_number'] : ''),
			'entry_city_birth'	 => (isset($_POST['billing_city_birth']) ? $_POST['billing_city_birth'] : ''),
			'entry_state'		  => (isset($_POST['billing_state']) ? $_POST['billing_state'] : 'none'),
			'entry_country_id'	 => $_POST['billing_country'],
			'entry_zone_id'		=> (isset($_POST['billing_state']) ? $_POST['billing_state'] : '0')
		);
		$userAccount->plugins['addressBook']->addAddressEntry('billing', $billingAddressArray);
		$userAccount->setFirstName($_POST['billing_firstname']);
		$userAccount->setLastName($_POST['billing_lastname']);
		if (isset($_POST['shipping_diff'])){
			$accountValidation = array(
				'entry_firstname'	  => $_POST['shipping_firstname'],
				'entry_lastname'	   => $_POST['shipping_lastname'],
				'entry_street_address' => $_POST['shipping_street_address'],
				'entry_postcode'	   => $_POST['shipping_postcode'],
				'entry_city'		   => $_POST['shipping_city'],
				'entry_country_id'	 => $_POST['shipping_country'],
			);

			if (array_key_exists('shipping_gender', $_POST)){
				$accountValidation['entry_gender'] = $_POST['shipping_gender'];
			}
			if (array_key_exists('shipping_dob', $_POST)){
				$accountValidation['entry_dob'] = $_POST['shipping_dob'];
			}
			if (array_key_exists('shipping_suburb', $_POST)){
				$accountValidation['entry_suburb'] = $_POST['shipping_suburb'];
			}
			if (array_key_exists('shipping_state', $_POST)){
				$accountValidation['entry_state'] = $_POST['shipping_state'];
			}
			if (array_key_exists('shipping_fiscal_code', $_POST)){
				$accountValidation['entry_cif'] = $_POST['shipping_fiscal_code'];
			}
			if (array_key_exists('shipping_vat_number', $_POST)){
				$accountValidation['entry_vat'] = $_POST['shipping_vat_number'];
			}
			if (array_key_exists('shipping_company', $_POST)){
				$accountValidation['entry_company'] = $_POST['shipping_company'];
			}
			if (array_key_exists('shipping_telephone', $_POST)){
				$accountValidation['telephone'] = $_POST['shipping_telephone'];
			}
			if (array_key_exists('shipping_fax', $_POST)){
				$accountValidation['fax'] = $_POST['shipping_fax'];
			}

			$error = $userAccount->validate($accountValidation);

			$shippingAddressArray = array(
				'entry_gender'		 => (isset($_POST['shipping_gender']) ? $_POST['shipping_gender'] : 'm'),
				'entry_dob'			=> (isset($_POST['shipping_dob']) ? $_POST['shipping_dob'] : ''),
				'entry_company'		=> (isset($_POST['shipping_company']) ? $_POST['shipping_company'] : ''),
				'entry_firstname'	  => $_POST['shipping_firstname'],
				'entry_lastname'	   => $_POST['shipping_lastname'],
				'entry_street_address' => $_POST['shipping_street_address'],
				'entry_suburb'		 => (isset($_POST['shipping_suburb']) ? $_POST['shipping_suburb'] : ''),
				'entry_postcode'	   => $_POST['shipping_postcode'],
				'entry_city'		   => $_POST['shipping_city'],
				'entry_cif'			=> (isset($_POST['shipping_fiscal_code']) ? $_POST['shipping_fiscal_code'] : ''),
				'entry_vat'			=> (isset($_POST['shipping_vat_number']) ? $_POST['shipping_vat_number'] : ''),
				'entry_state'		  => (isset($_POST['shipping_state']) ? $_POST['shipping_state'] : 'none'),
				'entry_country_id'	 => $_POST['shipping_country'],
				'entry_zone_id'		=> (isset($_POST['shipping_state']) ? $_POST['shipping_state'] : '0')
			);
			$userAccount->plugins['addressBook']->addAddressEntry('delivery', $shippingAddressArray);
		}
		else {
			$userAccount->plugins['addressBook']->addAddressEntry('delivery', $billingAddressArray);
		}

		if (isset($_POST['pickup_diff'])){
			$accountValidation = array(
				'entry_firstname'	  => $_POST['pickup_firstname'],
				'entry_lastname'	   => $_POST['pickup_lastname'],
				'entry_street_address' => $_POST['pickup_street_address'],
				'entry_postcode'	   => $_POST['pickup_postcode'],
				'entry_city'		   => $_POST['pickup_city'],
				'entry_country_id'	 => $_POST['pickup_country'],
			);

			if (array_key_exists('pickup_gender', $_POST)){
				$accountValidation['entry_gender'] = $_POST['pickup_gender'];
			}
			if (array_key_exists('pickup_dob', $_POST)){
				$accountValidation['entry_dob'] = $_POST['pickup_dob'];
			}
			if (array_key_exists('pickup_suburb', $_POST)){
				$accountValidation['entry_suburb'] = $_POST['pickup_suburb'];
			}
			if (array_key_exists('pickup_state', $_POST)){
				$accountValidation['entry_state'] = $_POST['pickup_state'];
			}
			if (array_key_exists('pickup_fiscal_code', $_POST)){
				$accountValidation['entry_cif'] = $_POST['pickup_fiscal_code'];
			}
			if (array_key_exists('pickup_vat_number', $_POST)){
				$accountValidation['entry_vat'] = $_POST['pickup_vat_number'];
			}
			if (array_key_exists('pickup_company', $_POST)){
				$accountValidation['entry_company'] = $_POST['pickup_company'];
			}
			if (array_key_exists('pickup_telephone', $_POST)){
				$accountValidation['telephone'] = $_POST['pickup_telephone'];
			}
			if (array_key_exists('pickup_fax', $_POST)){
				$accountValidation['fax'] = $_POST['pickup_fax'];
			}

			$error = $userAccount->validate($accountValidation);

			$pickupAddressArray = array(
				'entry_gender'		 => (isset($_POST['pickup_gender']) ? $_POST['pickup_gender'] : 'm'),
				'entry_dob'			=> (isset($_POST['pickup_dob']) ? $_POST['pickup_dob'] : ''),
				'entry_company'		=> $_POST['pickup_company'],
				'entry_firstname'	  => $_POST['pickup_firstname'],
				'entry_lastname'	   => $_POST['pickup_lastname'],
				'entry_street_address' => $_POST['pickup_street_address'],
				'entry_suburb'		 => (isset($_POST['pickup_suburb']) ? $_POST['pickup_suburb'] : ''),
				'entry_postcode'	   => $_POST['pickup_postcode'],
				'entry_cif'			=> (isset($_POST['pickup_fiscal_code']) ? $_POST['pickup_fiscal_code'] : ''),
				'entry_vat'			=> (isset($_POST['pickup_vat_number']) ? $_POST['pickup_vat_number'] : ''),
				'entry_city'		   => $_POST['pickup_city'],
				'entry_state'		  => $_POST['pickup_state'],
				'entry_country_id'	 => $_POST['pickup_country'],
				'entry_zone_id'		=> $_POST['pickup_state']
			);
			if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
				$userAccount->plugins['addressBook']->addAddressEntry('pickup', $pickupAddressArray);
			}
		}
		else {
			if (isset($shippingAddressArray)){
				if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
					$userAccount->plugins['addressBook']->addAddressEntry('pickup', $shippingAddressArray);
				}
			}
			else {
				if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
					$userAccount->plugins['addressBook']->addAddressEntry('pickup', $billingAddressArray);
				}
			}
		}

		$parsedAddress = $userAccount->plugins['addressBook']->getAddress('billing');
		$userAccount->setZoneId($parsedAddress['entry_zone_id']);
		$userAccount->setCountryId($parsedAddress['entry_country_id']);

		if (!empty($_POST['billing_telephone'])){
			$onePageCheckout->onePage['info']['telephone'] = $_POST['billing_telephone'];
			$userAccount->setTelephoneNumber($onePageCheckout->onePage['info']['telephone']);
		}

		if (!empty($_POST['billing_email_address'])){
			$onePageCheckout->onePage['info']['email_address'] = $_POST['billing_email_address'];
			$userAccount->setEmailAddress($onePageCheckout->onePage['info']['email_address']);
		}

		if (!empty($_POST['billing_dob'])){
			$onePageCheckout->onePage['info']['dob'] = $_POST['billing_dob'];
			$userAccount->setDateOfBirth($onePageCheckout->onePage['info']['dob']);
		}

		$onePageCheckout->onePage['createAccount'] = false;
		if ($error == false){
			if ($userAccount->isLoggedIn() === false){
				$onePageCheckout->onePage['info']['newsletter'] = (isset($_POST['newsletter']) ? '1' : '0');
				if (isset($_POST['create_account']) || $onePageCheckout->isMembershipCheckout() || $onePageCheckout->isGiftCertificateCheckout() || (sysConfig::get('ONEPAGE_ACCOUNT_CREATE') == 'required')){
					$onePageCheckout->onePage['createAccount'] = true;
					if (isset($_POST['password']) && isset($_POST['confirmation']) && $_POST['password'] == $_POST['confirmation'] && strlen($_POST['password']) >= sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH')){
						$onePageCheckout->onePage['info']['password'] = $_POST['password'];
					}
					else {
						if (strlen($_POST['password']) < sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH')){
							$error = true;
							$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_PASSWORD'), sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH')), 'error');
						}
						else {
							$error = true;
							$messageStack->addSession('pageStack', sprintf(sysLanguage::get('TEXT_PASSWORD_CONFIRMATION')), 'error');
						}
					}
				}
				else {
					$onePageCheckout->onePage['createAccount'] = true;
					$onePageCheckout->onePage['info']['password'] = tep_create_random_value(8);
				}
				if (!$error){
					$onePageCheckout->createCustomerAccount();
				}
			}
			$loadPage = 'shipping_payment.php';
			if ($messageStack->size('pageStack') > 0){
				$loadPage = 'addresses.php';
			}
		}
		else {
			$loadPage = 'addresses.php';
		}
	}
	elseif ($_POST['currentPage'] == 'payment_shipping') {
		unset($order->newOrder['orderTotals']);
		if (isset($_POST['payment_method'])){
			$PaymentModule = OrderPaymentModules::getModule($_POST['payment_method'], true);
			$onePageCheckout->setPaymentMethod($_POST['payment_method']);

			if ($onePageCheckout->isNormalCheckout() === true){
				if (Session::exists('credit_covers') === true && Session::get('credit_covers') === true){
					$PaymentModule = null;
				}
			}
		}
		if (isset($_POST['comments'])){
			$onePageCheckout->onePage['info']['comments'] = $_POST['comments'];
		}
		$onePageCheckout->loadOrdersVars();
		$onePageCheckout->fixTaxes();
		$order->newOrder['orderTotals'] = OrderTotalModules::process();
		if ($onePageCheckout->isMembershipCheckout()){
			$onePageCheckout->onePage['info']['account_action'] = 'new';
			Session::set('payment_rental', true);
		}
		EventManager::notify('CheckoutProcessPreProcess', $order);

		$checkPayment = true;
		$Module = OrderTotalModules::getModule('coupon');
		if ($Module !== false && $Module->isEnabled() === true){
			// Start - CREDIT CLASS Gift Voucher Contribution
			unset($_POST['gv_redeem_code']);
			//$orderTotalModules->collect_posts();
			//$orderTotalModules->pre_confirmation_check();
			if (Session::exists('credit_covers') === true){
				$checkPayment = false;
			}
			// End - CREDIT CLASS Gift Voucher Contribution
		}

		EventManager::notify('OnepageCheckoutProcessCheckout', $onePageCheckout);

		$remotePayment = false;
		$error = false;

		if ($checkPayment === true){
			if ($PaymentModule->hasFormUrl()){
				$remotePayment = true;
			}

			$e = $PaymentModule->validatePost();
			if ($e === false){
				$error = true;
				if (isset($e['redirectTo'])){
					$redirectUrl = $e['redirectTo'];
				}
				if (isset($e['errorMsg'])){
					$messageStack->addSession('pageStack', $e['errorMsg'], 'error');
				}
				else {
					$messageStack->addSession('pageStack', 'An error occured', 'error');
				}
			}
		}

		if ($error === false && $messageStack->size('pageStack') == 0){
			if ($onePageCheckout->isMembershipCheckout() === true){
				$order->info['is_rental'] = '1';
				$order->info['bill_attempts'] = '1';
			}

			$order->createOrder();
			$order->insertOrderTotals();
			$order->insertStatusHistory();
			// initialized for the email confirmation
			$products_ordered = '';

			if ($onePageCheckout->isMembershipCheckout() === true){
				for($i = 0, $n = sizeof($order->products); $i < $n; $i++){
					$order->insertMembershipProduct($order->products[$i], &$products_ordered);
				}

				$planID = $onePageCheckout->onePage['rentalPlan']['id'];

				if (isset($onePageCheckout->onePage['info']['account_action']) === true){
					if (isset($onePageCheckout->onePage['info']['payment'])){
						$paymentInfo = $onePageCheckout->onePage['info']['payment'];
						$rentalPlan = $onePageCheckout->onePage['rentalPlan'];

						$membershipMonths = $rentalPlan['months'];
						$membershipDays = $rentalPlan['days'];
						$numberOfRentals = $rentalPlan['no_of_titles'];
						$paymentTerm = 'N'; //$rentalPlan['pay_term'];
						$billPrice = tep_add_tax($rentalPlan['price'], $rentalPlan['tax_rate']);

						$nextBillDate = strtotime('+' . $membershipMonths . ' month +' . $membershipDays . ' day');
						if (isset($paymentTerm)){
							if ($paymentTerm == 'M'){
								$nextBillDate = strtotime('+1 month');
							}
							elseif ($paymentTerm == 'Y') {
								$nextBillDate = strtotime('+12 month');
							}
						}

						$freeTrialEnds = time();
						if ($rentalPlan['free_trial'] > 0){
							$freeTrialPeriod = $rentalPlan['free_trial'];
							if ($rentalPlan['free_trial'] > 0){
								$nextBillDate = strtotime('+' . $freeTrialPeriod . ' day');
								$freeTrialEnds = strtotime('+' . $freeTrialPeriod . ' day');
							}

							if ($freeTrialEnds > time() && $rentalPlan['free_trial_amount'] > 0){
								$billPrice = tep_add_tax($rentalPlan['free_trial_amount'], $rentalPlan['tax_rate']);
							}
						}

						$membership =& $userAccount->plugins['membership'];

						$membership->setPlanId($planID);
						$membership->setActivationStatus('N');
						$membership->setFreeTrailEnd($freeTrialEnds);
						$membership->setNextBillDate($nextBillDate);
						$membership->setPaymentTerm($paymentTerm);
						$membership->setPaymentMethod($onePageCheckout->onePage['info']['payment']['id']);
						$membership->setRentalAddress($userAccount->plugins['addressBook']->getDefaultAddressId());
						if (!empty($paymentInfo['cardDetails']['cardNumber'])){
							$membership->setCreditCardNumber($paymentInfo['cardDetails']['cardNumber']);
							$membership->setCreditCardExpirationDate($paymentInfo['cardDetails']['cardExpMonth'] . $paymentInfo['cardDetails']['cardExpYear']);
							if (!empty($paymentInfo['cardDetails']['cardCvvNumber'])){
								$membership->setCreditCardCvvNumber($paymentInfo['cardDetails']['cardCvvNumber']);
							}
						}
						if ($membership->isRentalMember() === true){
							$membership->updateMembership();
						}else{
							$membership->setMembershipStatus('M');
							$membership->createNewMembership();
						}
					}
				}
			}
			else {
				foreach($ShoppingCart->getProducts() as $cartProduct){
					$order->insertOrderedProduct($cartProduct, &$products_ordered);

					EventManager::notify('CheckoutProcessInsertOrderedProduct', $cartProduct, &$products_ordered);
				}
			}

			EventManager::notify('CheckoutProcessPostProcess', &$order);

			$PaymentModule->beforeProcessPayment();

			if ($remotePayment === true){
				$hiddenFields = $PaymentModule->getHiddenFields();
				if (empty($hiddenFields)){
					$hiddenFields = $onePageCheckout->drawHiddenFieldsFromArray($_POST);
				}

				$pageHtml = '<input type="hidden" name="currentPage" id="currentPage" value="processing">' .
					$PaymentModule->getRemoteLoadingPage() .
					htmlBase::newElement('button')->usePreset('continue')->setType('submit')->hide()->draw() .
					$hiddenFields .
					'<script>' .
					'$(\'form[name=checkout]\').attr(\'action\',\'' . $PaymentModule->getFormUrl() . '\');' .
					'setTimeout("$(\'form[name=checkout]\').submit()", 3000);' .
					'</script>';
			}
			else {
				if ($onePageCheckout->isNormalCheckout()){
					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
						$temp = $order->info['total'];
						$order->info['total'] = 0;
						$PaymentModule->processPayment();
						$order->info['total'] = $temp;
					}
					else {
						$PaymentModule->processPayment();
					}

					if ($messageStack->size('pageStack') > 0){
						$error = true;
						$PaymentModule->afterProcessPayment(false);
						$loadPage = 'shipping_payment.php';
					}
					else {
						$PaymentModule->afterProcessPayment(true);
						$order->sendNewOrderEmail();

						// load the after_process function from the payment modules
						$PaymentModule->afterOrderProcess();
					}
				}
				elseif ($onePageCheckout->isMembershipCheckout() === true) {
					if ($billPrice > 0){
						$PaymentModule->processPayment();
						if ($messageStack->size('pageStack') > 0){
							$error = true;
							$PaymentModule->afterProcessPayment(false);
							$loadPage = 'shipping_payment.php';
						}
						else {
							$PaymentModule->afterProcessPayment(true);
						}
					}

					if ($messageStack->size('pageStack') == 0){
						$membership->updateActivationStatus('Y');
						$order->sendNewOrderEmail();

						$PaymentModule->afterOrderProcess();
					}
					//$ShoppingCart->emptyCart(true);
				}
				elseif ($onePageCheckout->isGiftCertificateCheckout() === true) {
					$PaymentModule->processPayment();

					if ($messageStack->size('pageStack') > 0){
						$error = true;
						$loadPage = 'shipping_payment.php';
						$PaymentModule->afterProcessPayment(false);
					}
					else {
						$PaymentModule->afterProcessPayment(true);
						$order->sendNewOrderEmail();
						$PaymentModule->afterOrderProcess();
					}
				}
				else{
					$messageStack->add('pageStack', 'Error: Unknown Checkout Type');
				}

				if ($messageStack->size('pageStack') == 0){
					$loadPage = 'success.php';
				}
			}
		}
		else {
			$loadPage = 'shipping_payment.php';
		}
	}
}

if (!isset($pageHtml)){
	ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/' . $loadPage);
	$pageHtml = ob_get_contents();
	ob_end_clean();
}

if ($messageStack->size('pageStack') > 0){
	$error = true;
	$pageHtml = $messageStack->output('pageStack') . $pageHtml;
}
EventManager::attachActionResponse(array(
	'success'	=> true,
	'pageHtml'   => $pageHtml,
	'isShipping' => (isset($_POST['shipping_diff']) ? true : false),
	'isPickup'   => (isset($_POST['pickup_diff']) ? true : false)
), 'json');
?>