<div><?php
	echo sysLanguage::get('TEXT_NEW_RENTAL_CUSTOMER') . '<br><br>' . sprintf(sysLanguage::get('TEXT_NEW_RENTAL_CUSTOMER_INTRODUCTION'), sysConfig::get('STORE_NAME')) . '<br>';
	?></div>
<div style="text-align:right"><br /><?php
	$rentalAccountButton = htmlBase::newElement('button')
		->usePreset('continue')
		->setHref(itw_app_link('checkoutType=rental', 'checkout', 'default', 'SSL'));

	echo $rentalAccountButton->draw();
	?></div>