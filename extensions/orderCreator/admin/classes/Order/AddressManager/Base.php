<?php
/**
 * Address manager class for the order creator
 *
 * @package OrderCreator
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2011, I.T. Web Experts
 */

require(dirname(__FILE__) . '/Address.php');

class OrderCreatorAddressManager extends OrderAddressManager
{

	/**
	 * @param array|null $addressArray
	 */
	public function __construct(array $addressArray = null) {
		$this->addressHeadings['customer'] = sysLanguage::get('TEXT_ADDRESS_CUSTOMER');

		if (sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_BILLING_ADDRESS') == 'True'){
			$this->addressHeadings['billing'] = sysLanguage::get('TEXT_ADDRESS_BILLING');
		}
		if (sysConfig::get('EXTENSION_ORDER_CREATOR_SHOW_DELIVERY_ADDRESS') == 'True'){
			$this->addressHeadings['delivery'] = sysLanguage::get('TEXT_ADDRESS_DELIVERY');
		}

		if (sysConfig::exists('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') && sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHOOSE_PICKUP') == 'True'){
			$this->addressHeadings['pickup'] = sysLanguage::get('TEXT_ADDRESS_PICKUP');
		}

		if (is_null($addressArray) === false){
			foreach($addressArray as $type => $aInfo){
				if (isset($this->addressHeadings[$type])){
					$this->addresses[$type] = new OrderCreatorAddress($aInfo);
				}
			}
		}
		else {
			foreach($this->addressHeadings as $type => $heading){
				$this->addresses[$type] = new OrderCreatorAddress(array(
					'address_type' => $type
				));
			}
		}
	}

	/**
	 * @param OrderCreatorAddress $addressObj
	 */
	public function addAddressObj(OrderCreatorAddress $addressObj) {
		$this->addresses[$addressObj->getAddressType()] = $addressObj;
	}

	/**
	 *
	 */
	public function updateFromPost() {
		if (!isset($_POST['address']['billing'])){
			$_POST['address']['billing'] = $_POST['address']['customer'];
		}
		if (!isset($_POST['address']['delivery'])){
			$_POST['address']['delivery'] = $_POST['address']['customer'];
		}

		foreach($_POST['address'] as $type => $aInfo){
			if (isset($this->addresses[$type])){
				$this->addresses[$type]->setName($aInfo['entry_name']);
				$this->addresses[$type]->setCompany($aInfo['entry_company']);
				$this->addresses[$type]->setStreetAddress($aInfo['entry_street_address']);
				$this->addresses[$type]->setSuburb($aInfo['entry_suburb']);
				$this->addresses[$type]->setCity($aInfo['entry_city']);
				$this->addresses[$type]->setPostcode($aInfo['entry_postcode']);
				$this->addresses[$type]->setState($aInfo['entry_state']);
				$this->addresses[$type]->setCountry($aInfo['entry_country']);
				if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
					$this->addresses[$type]->setCityBirth($aInfo['entry_city_birth']);
				}
				if (sysConfig::get('ACCOUNT_DATE_OF_BIRTH') == 'true'){
					$this->addresses[$type]->setDOB($aInfo['entry_dob']);
				}
				if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
					$this->addresses[$type]->setFiscalCode($aInfo['entry_cif']);
				}
				if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
					$this->addresses[$type]->setVATNumber($aInfo['entry_vat']);
				}
			}
		}
	}

	/**
	 * @param Doctrine_Collection $CollectionObj
	 */
	public function addAllToCollection(Doctrine_Collection &$CollectionObj) {
		$CollectionObj->clear();
		foreach($this->addresses as $type => $addressObj){
			$Address = new OrdersAddresses();
			$Address->address_type = $type;
			$Address->entry_format_id = $addressObj->getFormatId();
			$Address->entry_name = $addressObj->getName();
			if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
				$Address->entry_company = $addressObj->getCompany();
			}
			$Address->entry_street_address = $addressObj->getStreetAddress();
			$Address->entry_suburb = $addressObj->getSuburb();
			$Address->entry_city = $addressObj->getCity();
			$Address->entry_postcode = $addressObj->getPostcode();
			$Address->entry_state = $addressObj->getState();
			$Address->entry_country = $addressObj->getCountry();
			if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
				$Address->entry_city_birth = $addressObj->getCityBirth();
			}
			if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
				$Address->entry_cif = $addressObj->getCIF();
			}
			if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
				$Address->entry_vat = $addressObj->getVAT();
			}
			if (sysConfig::get('ACCOUNT_DATE_OF_BIRTH') == 'true'){
				$Address->entry_dob = $addressObj->getDateOfBirth();
			}
			$CollectionObj->add($Address);
		}
	}

	/**
	 *
	 */
	public function saveAll() {
		$OrdersAddresses = Doctrine_Core::getTable('OrdersAddresses');
		foreach($this->addresses as $type => $addressObj){
			if (is_null($this->orderId) === true){
				$Address = $OrdersAddresses->create();
			}
			else {
				$Address = $OrdersAddresses->find($addressObj->getId());
				if (!$Address){
					$Address = $OrdersAddresses->create();
					$Address->orders_id = $this->orderId;
					$Address->address_type = $addressObj->getType();
				}
			}

			$Address->entry_format_id = $addressObj->getFormatId();
			$Address->entry_name = $addressObj->getName();

			if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
				$Address->entry_company = $addressObj->getCompany();
			}

			$Address->entry_street_address = $addressObj->getStreetAddress();
			$Address->entry_suburb = $addressObj->getSuburb();
			$Address->entry_city = $addressObj->getCity();
			$Address->entry_postcode = $addressObj->getPostcode();
			$Address->entry_state = $addressObj->getState();
			$Address->entry_country = $addressObj->getCountry();
			if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
				$Address->entry_cif = $addressObj->getCIF();
			}
			if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
				$Address->entry_vat = $addressObj->getVAT();
			}
			$Address->entry_city_birth = $addressObj->getCityBirth();
			$Address->entry_dob = $addressObj->getDateOfBirth();

			$Address->save();
		}
	}

	/**
	 * @return string
	 */
	public function editAll() {
		global $Editor;
		$addressesTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0)
			->addClass('addressTable')
			->css('width', '100%');

		$addressesRow = array();
		foreach($this->addressHeadings as $type => $heading){
			$addressObj = $this->addresses[$type];
			$addressTable = htmlBase::newElement('table')
				->setCellPadding(2)
				->setCellSpacing(0)
				->css('width', '100%');

			$addressTable->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main',
						'valign' => 'top',
						'text'   => '<b>' . $heading . '</b>'
					)
				)
			));

			$customerId = '';
			if ($Editor->getCustomerId() > 0){
				$customerId = htmlBase::newElement('input')
					->setType('hidden')
					->setName('customers_id')
					->val((int)$Editor->getCustomerId())
					->draw();
			}

			$addressTable->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'main ' . $type . 'Address',
						'valign' => 'top',
						'text'   => $this->editAddress($addressObj) . $customerId
					)
				)
			));

			$addressesRow[] = array(
				'valign' => 'top',
				'text'   => $addressTable
			);
		}
		$addressesTable->addBodyRow(array(
			'columns' => $addressesRow
		));

		return $addressesTable->draw();
	}

	/**
	 * @param $type
	 * @return string
	 */
	public function editAddress($type) {
		$htmlTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0);

		$Address = null;
		if (is_object($type) === true){
			$Address = $type;
		}
		elseif (array_key_exists($type, $this->addresses) === true) {
			$Address = $this->addresses[$type];
		}

		if (is_null($Address) === true){
			$aType = 'customer';
		}
		else {
			$aType = $Address->getAddressType();
		}

		$htmlTable->addClass('addressTable ' . $aType . 'Address');

		$nameInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_NAME'))
			->setName('address[' . $aType . '][entry_name]');

		$companyInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_COMPANY'))
			->setName('address[' . $aType . '][entry_company]');

		$addressInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_STREET_ADDRESS'))
			->setName('address[' . $aType . '][entry_street_address]');

		$suburbInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_SUBURB'))
			->setName('address[' . $aType . '][entry_suburb]');

		$cityInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_CITY'))
			->setName('address[' . $aType . '][entry_city]');

		$cifInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_CIF'))
			->setName('address[' . $aType . '][entry_cif]');

		$vatInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_VAT'))
			->setName('address[' . $aType . '][entry_vat]');

		$cityBirthInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_CITY_BIRTH'))
			->setName('address[' . $aType . '][entry_city_birth]');

		$dobInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_DOB'))
			->setName('address[' . $aType . '][entry_dob]');

		$postcodeInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_POST_CODE'))
			->setName('address[' . $aType . '][entry_postcode]');

		$stateInput = htmlBase::newInput()
			->setPlaceholder(sysLanguage::get('ENTRY_STATE'))
			->setName('address[' . $aType . '][entry_state]');

		$countryInput = htmlBase::newSelectbox()
			->addClass('country')
			->attr('data-address_type', $aType)
			->setName('address[' . $aType . '][entry_country]');

		$Qcountries = Doctrine_Query::create()
			->select('countries_name')
			->from('Countries')
			->orderBy('countries_name')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qcountries as $cInfo){
			$countryInput->addOption($cInfo['countries_name'], $cInfo['countries_name']);
		}

		if (is_null($Address) === false){
			$Qcountry = Doctrine_Query::create()
				->select('countries_id')
				->from('Countries')
				->where('countries_name = ?', $Address->getCountry())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcountry){
				$addressCountryId = $Qcountry[0]['countries_id'];

				$stateInput = htmlBase::newElement('selectbox')->setName('address[' . $aType . '][entry_state]')
					->css(array('width' => '150px'));

				$Qzones = Doctrine_Query::create()
					->select('zone_name')
					->from('Zones')
					->where('zone_country_id = ?', $addressCountryId)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				foreach($Qzones as $zInfo){
					$stateInput->addOption($zInfo['zone_name'], $zInfo['zone_name']);
				}
			}
		}

		if (!isset($stateInput)){
		}

		if (is_null($Address) === false){
			$nameInput->val($Address->getName());
			if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
				$companyInput->val($Address->getCompany());
			}

			$addressInput->val($Address->getStreetAddress());
			$suburbInput->val($Address->getSuburb());
			$cityInput->val($Address->getCity());
			if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
				$cifInput->val($Address->getCIF());
			}
			if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
				$vatInput->val($Address->getVAT());
			}
			$dobInput->val($Address->getDateOfBirth());
			$cityBirthInput->val($Address->getCityBirth());
			$postcodeInput->val($Address->getPostcode());

			if ($stateInput->isType('select')){
				$stateInput->selectOptionByValue($Address->getState());
			}
			else {
				$stateInput->val($Address->getState());
			}

			if ($Address->getCountry() == ''){
				$countryInput->selectOptionByValue('United Kingdom');
			}
			else {
				$countryInput->selectOptionByValue($Address->getCountry());
			}
		}

		if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
			$htmlTable->addBodyRow(array(
				'columns' => array(
					array('colspan' => 3, 'text' => $companyInput)
				)
			));
		}

		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 3, 'text' => $nameInput)
			)
		));

		if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
			$htmlTable->addBodyRow(array(
				'columns' => array(
					array('colspan' => 3, 'text' => $cifInput)
				)
			));
		}

		if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
			$htmlTable->addBodyRow(array(
				'columns' => array(
					array('colspan' => 3, 'text' => $vatInput)
				)
			));
		}

		if (sysConfig::get('ACCOUNT_DOB') == 'true'){
			$htmlTable->addBodyRow(array(
				'columns' => array(
					array('colspan' => 3, 'text' => $dobInput)
				)
			));
		}

		if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
			$htmlTable->addBodyRow(array(
				'columns' => array(
					array('colspan' => 3, 'text' => $cityBirthInput)
				)
			));
		}

		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 3, 'text' => $addressInput)
			)
		));

		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('css' => array('width' => '40%'), 'text' => $cityInput),
				array('css' => array('width' => '40%'), 'addCls' => 'stateCol', 'text' => $stateInput),
				array('css' => array('width' => '20%'), 'text' => $postcodeInput)
			)
		));

		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 3, 'text' => $suburbInput)
			)
		));

		$htmlTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 3, 'text' => $countryInput)
			)
		));

		EventManager::notify('OrderEditorAddressOnEdit', $htmlTable);

		return $htmlTable->draw();
	}

	/**
	 * @return string
	 */
	public function getCopyToButtons() {
		$buttons = '';
		foreach($this->addressHeadings as $addressType => $addressHeading){
			if ($addressType == 'customer') {
				continue;
			}

			$buttons .= htmlBase::newElement('button')
				->addClass('addressCopyButton')
				->attr('data-copy_from', 'customer')
				->attr('data-copy_to', $addressType)
				->setText(substr($addressHeading, 0, strpos($addressHeading, ' ')))
				->draw();
		}

		return sysLanguage::get('TEXT_COPY_TO') . $buttons;
	}

	public function jsonDecode($data){
		$Decoded = json_decode($data, true);
		foreach($Decoded['addresses'] as $Type => $aInfo){
			$this->addresses[$Type] = new OrderCreatorAddress(array_merge($aInfo['addressInfo'], array(
				'address_type' => $Type
			)));
		}
	}
}

?>