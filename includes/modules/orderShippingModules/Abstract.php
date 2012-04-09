<?php
class OrderShippingModuleBase extends ModuleBase
{

	/**
	 * @var array
	 */
	private $output = array();

	/**
	 * @var int
	 */
	private $taxClass = 0;

	/**
	 * @var int
	 */
	private $shippingZone = 0;

	/**
	 * @param string $code
	 * @param bool $forceEnable
	 * @param bool $moduleDir
	 */
	public function init($code, $forceEnable = false, $moduleDir = false) {
		$this->import(new Installable);
		$this->import(new SortedDisplay);

		$this->setModuleType('orderShipping');
		parent::init($code, $forceEnable, $moduleDir);

		if ($this->configExists($this->getModuleInfo('zone_key'))){
			$this->shippingZone = (int)$this->getConfigData($this->getModuleInfo('zone_key'));
		}

		if ($this->configExists($this->getModuleInfo('tax_class_key'))){
			$this->taxClass = (int)$this->getConfigData($this->getModuleInfo('tax_class_key'));
		}

		if ($this->configExists($this->getModuleInfo('sort_key'))){
			$this->setDisplayOrder((int)$this->getConfigData($this->getModuleInfo('sort_key')));
		}
	}

	/**
	 * @return bool
	 */
	public function getStatus() {
		return $this->isEnabled();
	}

	/**
	 * @return RentalStoreUser
	 */
	public function &getUserAccount() {
		global $onePageCheckout;
		if (isset($onePageCheckout) && is_object($onePageCheckout)){
			$userAccount = &$onePageCheckout->getUserAccount();
		}
		elseif (Session::exists('pointOfSale') === true) {
			$pointOfSale = &Session::getReference('pointOfSale');
			$userAccount = &$pointOfSale->getUserAccount();
		}
		return $userAccount;
	}

	/**
	 * @return bool|array
	 */
	public function getDeliveryAddress() {
		global $Editor, $userAccount;
		if (isset($Editor) && is_object($Editor)){
			$Address = $Editor->AddressManager->getAddress('delivery');
			return $Address->toArray();
		}

		if (isset($userAccount)){
			$addressBook = $userAccount->plugins['addressBook'];
		}
		if (isset($addressBook) && is_object($addressBook)){
			if ($addressBook->entryExists('delivery') === true){
				$deliveryAddress = $addressBook->getAddress('delivery');
			}
			else {
				$deliveryAddress = $addressBook->getAddress('billing');
			}
			return $deliveryAddress;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function updateStatus() {
		global $order, $onePageCheckout;

		if (is_object($order) && $this->isEnabled() === true && $this->shippingZone > 0){
			$deliveryAddress = $this->getDeliveryAddress();
			$check_flag = false;
			$Qcheck = Doctrine_Query::create()
				->select('zt.zone_id, z.geo_zone_id')
				->from('GeoZones z')
				->leftJoin('z.ZonesToGeoZones zt')
				->where('z.geo_zone_id = ?', $this->shippingZone)
				->andWhere('zt.zone_country_id = ?', $deliveryAddress['entry_country_id'])
				->orderBy('zt.zone_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if ($Qcheck){
				foreach($Qcheck as $zInfo){
					foreach($zInfo['ZonesToGeoZones'] as $iInfo){
						if ($iInfo['zone_id'] < 1){
							$check_flag = true;
							break;
						}
						elseif ($iInfo['zone_id'] == $deliveryAddress['entry_zone_id']) {
							$check_flag = true;
							break;
						}
					}
				}
			}

			if ($check_flag == false){
				$this->setEnabled(false);
			}
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function check() {
		return ($this->isInstalled() === true);
	}

	/**
	 * @param $data
	 */
	public function addOutput($data) {
		$this->output[] = $data;
	}

	/**
	 * @return int
	 */
	public function getTaxClass() {
		return $this->taxClass;
	}

	/**
	 * @return array
	 */
	public function getOutput() {
		return $this->output;
	}

	/**
	 * @return array
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * @param string $method
	 */
	public function quote($method = '') {
		die('Quote function not overwritten.');
	}

	/**
	 * @param $amount
	 * @return string
	 */
	public function formatAmount($amount) {
		global $order, $currencies;
		return $currencies->format($amount, true, $order->info['currency'], $order->info['currency_value']);
	}

	/**
	 * @param $module
	 * @param $moduleConfig
	 */
	public function onInstall(&$module, &$moduleConfig) {
	}
}

?>