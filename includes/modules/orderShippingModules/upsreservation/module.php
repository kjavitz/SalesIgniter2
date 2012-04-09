<?php
class OrderShippingUpsReservation extends OrderShippingModuleBase
{

	private $quotes;

	private $methods = array();

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('UPS Reservation');
		$this->setDescription('United Postal Service');

		$this->init('upsreservation');

		if ($this->isEnabled() === true){
			/*$this->types = array(
				'1DM'    => 'Next Day Air Early AM',
				'1DML'   => 'Next Day Air Early AM Letter',
				'1DA'    => 'Next Day Air',
				'1DAL'   => 'Next Day Air Letter',
				'1DAPI'  => 'Next Day Air Intra (Puerto Rico)',
				'1DP'    => 'Next Day Air Saver',
				'1DPL'   => 'Next Day Air Saver Letter',
				'2DM'    => '2nd Day Air AM',
				'2DML'   => '2nd Day Air AM Letter',
				'2DA'    => '2nd Day Air',
				'2DAL'   => '2nd Day Air Letter',
				'3DS'    => '3 Day Select',
				'GND'    => 'Ground',
				'GNDCOM' => 'Ground Commercial',
				'GNDRES' => 'Ground Residential',
				'STD'    => 'Canada Standard',
				'XPR'    => 'Worldwide Express',
				'XPRL'   => 'worldwide Express Letter',
				'XDM'    => 'Worldwide Express Plus',
				'XDML'   => 'Worldwide Express Plus Letter',
				'XPD'    => 'Worldwide Expedited'
			);*/

			if ($this->isEnabled() === true){
				$Qmethods = Doctrine_Query::create()
					->from('ModulesShippingUpsReservationMethods m')
					->leftJoin('m.ModulesShippingUpsReservationMethodsDescription md')
					->orderBy('sort_order')
					->execute()
					->toArray(true);
				if ($Qmethods){
					foreach($Qmethods as $mInfo){
						$this->methods[$mInfo['method_id']] = array(
							'status' => $mInfo['method_status'],
							'upscode' => $mInfo['method_upscode'],
							'text' => $mInfo['ModulesShippingUpsReservationMethodsDescription'][Session::get('languages_id')]['method_text'],
							'markup' => $mInfo['method_markup'],
							'days_before' => $mInfo['method_days_before'],
							'days_after' => $mInfo['method_days_after'],
							'sort_order' => $mInfo['sort_order'],
							'default' => $mInfo['method_default']
						);
						foreach(sysLanguage::getLanguages() as $lInfo){
							if (isset($mInfo['ModulesShippingUpsReservationMethodsDescription'][$lInfo['id']])){
								$this->methods[$mInfo['method_id']][$lInfo['id']] = array(
									'text' => $mInfo['ModulesShippingUpsReservationMethodsDescription'][$lInfo['id']]['method_text']
								);
							}
						}
					}
				}
			}

			$this->packagePickup = $this->getConfigData('MODULE_ORDER_SHIPPING_UPS_RESERVATION_PICKUP');
			$this->packageContainer = $this->getConfigData('MODULE_ORDER_SHIPPING_UPS_RESERVATION_PACKAGE');
			$this->addressType = $this->getConfigData('MODULE_ORDER_SHIPPING_UPS_RESERVATION_RES');
			$this->handlingCost = ($this->getConfigData('MODULE_SHIPPING_UPS_RESERVATION_HANDLING') != '')
				? $this->getConfigData('MODULE_SHIPPING_UPS_RESERVATION_HANDLING') : 0;
		}
	}

	public function getUpsTitle($upscode) {
		foreach($this->methods as $method){
			if (isset($method['upscode']) && $method['upscode'] == $upscode){
				if ($method[Session::get('languages_id')]['text']){
					return $method[Session::get('languages_id')]['text'];
				}
				else {
					return $method['text'];
				}
			}
		}
		return 'none';
	}

	public function getType(){
		return $this->type;
	}

	public function getNumBoxes(&$shipping_weight, &$shipping_num_boxes){
		$boxWeight = sysConfig::get('SHIPPING_BOX_WEIGHT');
		$boxPadding = sysConfig::get('SHIPPING_BOX_PADDING');
		$boxMaxWeight = sysConfig::get('SHIPPING_MAX_WEIGHT');


		if ($boxWeight >= $shipping_weight * $boxPadding / 100) {
			$shipping_weight = $shipping_weight + $boxWeight;
		} else {
			$shipping_weight = $shipping_weight + ($shipping_weight * $boxPadding / 100);
		}

		if ($shipping_weight > $boxMaxWeight) { // Split into many boxes
			$shipping_num_boxes = ceil($shipping_weight / $boxMaxWeight);
			$shipping_weight = $shipping_weight / $shipping_num_boxes;
		}
	}
	
	public function quote($method = '', $shipping_weight = -1){
		global $order,  $userAccount, $App;
		$shipping_num_boxes = 1;
		$this->getNumBoxes($shipping_weight, $shipping_num_boxes);

		if ( isset($method) && !empty($method)) {
			$prod = $method;
		} else {
			$prod = 'GND';
		}


		$this->_upsProduct($prod);
		if($App->getEnv() == 'catalog'){
			$deliveryAddress = $this->getDeliveryAddress();
		}else{
			global $Editor;
			if(isset($Editor)){
				$deliveryAddress = $Editor->AddressManager->getAddress('delivery')->toArray();
			}
		}
		if (isset($deliveryAddress) && $method != 'all'){
			$deliveryCountry = OrderShippingModules::getCountryInfo($deliveryAddress['entry_country_id']);
			$country_name = tep_get_countries(sysConfig::get('SHIPPING_ORIGIN_COUNTRY'), true);
			$this->_upsOrigin(sysConfig::get('SHIPPING_ORIGIN_ZIP'), $deliveryCountry['countries_iso_code_2']);
			$this->_upsDest($deliveryAddress['entry_postcode'], $deliveryCountry['countries_iso_code_2']);

			$this->_upsRate($this->packagePickup);
			$this->_upsContainer($this->packageContainer);
			if ($shipping_weight == 0){
				$this->_upsWeight(1);
			}
			else {
				$this->_upsWeight($shipping_weight);
			}
			$this->_upsRescom($this->addressType);
			$upsQuote = $this->_upsGetQuote();

			$this->quotes = array(
				'id' => $this->getCode(),
				'module' => $this->getTitle(),
				'icon' => tep_image(sysConfig::get('DIR_WS_TEMPLATE_IMAGES') . 'shipping_ups.gif', $this->getTitle()),
				'methods' => array()
			);

			if ((is_array($upsQuote)) && (sizeof($upsQuote) > 0)){
				if ($shipping_num_boxes > 0 && $shipping_weight > 0){
					$numBoxes = $shipping_num_boxes;
					$this->quotes['module'] .= ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . 'lbs)';
				}
				else {
					$numBoxes = 1;
				}

				$methods = array();
				$qsize = sizeof($upsQuote);
				for($i = 0; $i < $qsize; $i++){
					list($type, $cost) = each($upsQuote[$i]);
					if (isset($this->methods)){
						foreach($this->methods as $methodId => $mInfo){
							if ($mInfo['status'] == 'True' && ($method == 'method' . $methodId || $method == '')){
								if ($type == $mInfo['upscode']){
									$this->quotes['methods'][] = array(
										'id' => 'method' . $methodId,
										'title' => $mInfo['text'],
										'default' => $mInfo['default'],
										'cost' => ($cost + $this->handlingCost) * $numBoxes + ($cost + $this->handlingCost) * $numBoxes * ($mInfo['markup'] / 100),
										'days_before' => $mInfo['days_before'],
										'days_after' => $mInfo['days_after']
									);
								}
							}
						}
					}
				}

				$classId = $this->getTaxClass();
				if ($classId > 0){
					$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['country_id'], $deliveryAddress['zone_id']);
				}
			}
			else {
				$this->quotes['error'] = 'An error occured with the UPS shipping calculations.<br>' . $upsQuote . '<br>If you prefer to use UPS as your shipping method, please contact the store owner.';
			}
		}
		else {
			$this->quotes = array(
				'id' => $this->getCode(),
				'module' => $this->getTitle(),
				'methods' => array()
			);

			foreach($this->methods as $methodId => $mInfo){
				if ($mInfo['status'] == 'True' && ($method == 'method' . $methodId || $method == '')){
					$this->quotes['methods'][] = array(
						'id' => 'method' . $methodId,
						'title' => $mInfo['text'],
						'cost' => 0,
						'days_before' => $mInfo['days_before'],
						'days_after' => $mInfo['days_after']
					);
				}
			}

			$classId = $this->getTaxClass();
			if ($classId > 0){
				$deliveryAddress = $this->getDeliveryAddress();
				$this->quotes['tax'] = tep_get_tax_rate($classId, $deliveryAddress['country_id'], $deliveryAddress['zone_id']);
			}
		}
		return $this->quotes;
	}

	public function _upsProduct($prod) {
		$this->_upsProductCode = $prod;
	}

	public function _upsOrigin($postal, $country) {
		$this->_upsOriginPostalCode = $postal;
		$this->_upsOriginCountryCode = $country;
	}

	public function _upsDest($postal, $country) {
		$postal = str_replace(' ', '', $postal);

		if ($country == 'US'){
			$this->_upsDestPostalCode = substr($postal, 0, 5);
		}
		else {
			$this->_upsDestPostalCode = $postal;
		}

		$this->_upsDestCountryCode = $country;
	}

	public function _upsRate($foo) {
		switch($foo){
			case 'RDP':
				$this->_upsRateCode = 'Regular+Daily+Pickup';
				break;
			case 'OCA':
				$this->_upsRateCode = 'On+Call+Air';
				break;
			case 'OTP':
				$this->_upsRateCode = 'One+Time+Pickup';
				break;
			case 'LC':
				$this->_upsRateCode = 'Letter+Center';
				break;
			case 'CC':
				$this->_upsRateCode = 'Customer+Counter';
				break;
		}
	}

	public function _upsContainer($foo) {
		switch($foo){
			case 'CP': // Customer Packaging
				$this->_upsContainerCode = '00';
				break;
			case 'ULE': // UPS Letter Envelope
				$this->_upsContainerCode = '01';
				break;
			case 'UT': // UPS Tube
				$this->_upsContainerCode = '03';
				break;
			case 'UEB': // UPS Express Box
				$this->_upsContainerCode = '21';
				break;
			case 'UW25': // UPS Worldwide 25 kilo
				$this->_upsContainerCode = '24';
				break;
			case 'UW10': // UPS Worldwide 10 kilo
				$this->_upsContainerCode = '25';
				break;
		}
	}

	public function _upsWeight($foo) {
		$this->_upsPackageWeight = $foo;
	}

	public function _upsRescom($foo) {
		switch($foo){
			case 'RES': // Residential Address
				$this->_upsResComCode = '1';
				break;
			case 'COM': // Commercial Address
				$this->_upsResComCode = '2';
				break;
		}
	}

	public function _upsAction($action) {
		/* 3 - Single Quote
		4 - All Available Quotes */

		$this->_upsActionCode = $action;
	}

	public function _upsGetQuote() {

		$request = array(
			'accept_UPS_license_agreement=yes',
			'10_action=4',
			'13_product=' . $this->_upsProductCode,
			'14_origCountry=' . $this->_upsOriginCountryCode,
			'15_origPostal=' . $this->_upsOriginPostalCode,
			'19_destPostal=' . $this->_upsDestPostalCode,
			'22_destCountry=' . $this->_upsDestCountryCode,
			'23_weight=' . $this->_upsPackageWeight,
			'47_rate_chart=' . $this->_upsRateCode,
			'48_container=' . $this->_upsContainerCode,
			'49_residential=' . $this->_upsResComCode
		);

		$http = new httpClient();
		if ($http->Connect('www.ups.com', 80)){
			$http->addHeader('Host', 'www.ups.com');
			$http->addHeader('User-Agent', 'SalesIgniter');
			$http->addHeader('Connection', 'Close');

			if ($http->Get('/using/services/rave/qcostcgi.cgi?' . implode('&', $request))){
				$body = $http->getBody();
			}

			$http->Disconnect();
		}
		else {
			return 'error';
		}

		$body_array = explode("\n", $body);
		$returnval = array();
		$errorret = 'error'; // only return error if NO rates returned
		//print_r($body_array);
		$n = sizeof($body_array);
		for ($i=0; $i<$n; $i++) {
			$result = explode('%', $body_array[$i]);
			$errcode = substr($result[0], -1);
			switch ($errcode) {
				case 3:
					if (is_array($returnval)) $returnval[] = array($result[1] => $result[8]);
					break;
				case 4:
					if (is_array($returnval)) $returnval[] = array($result[1] => $result[8]);
					break;
				case 5:
					$errorret = $result[1];
					break;
				case 6:
					if (is_array($returnval)) $returnval[] = array($result[3] => $result[10]);
					break;
			}
		}
		if (empty($returnval)) $returnval = $errorret;

		return $returnval;
	}
}
?>