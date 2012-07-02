<?php
class PurchaseType_reservation_utilities
{

	private static $EventsCache = array();

	private static $GatesCache = array();

	private static $RentalTypesCache = array();

	public static $RentalPricingCache = array();

	private static $ProductPeriodCache = array();

	public static function getRentalPeriods()
	{
		$Qperiods = Doctrine_Query::create()
			->from('PayPerRentalPeriods p')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Qperiods;
	}

	public static function getProductPeriods($productId, $periodId = null)
	{
		if (!isset(self::$ProductPeriodCache[$productId][$periodId])){
			$ResultSet = Doctrine_Query::create()
				->from('ProductsPayPerPeriods p')
				->leftJoin('p.Period pd')
				->where('p.products_id = ?', $productId);

			if (is_null($periodId) === false){
				$ResultSet->andWhere('p.period_id = ?', $periodId);
			}

			self::$ProductPeriodCache[$productId][$periodId] = $ResultSet->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		}
		return self::$ProductPeriodCache[$productId][$periodId];
	}

	public static function getEvents($eventName = false)
	{
		if (!isset(self::$EventsCache[$eventName])){
			$Qevents = Doctrine_Query::create()
				->from('PayPerRentalEvents')
				->orderBy('events_date');

			if ($eventName !== false){
				$Qevents->where('event_name = ?', $eventName);
				$Result = $Qevents->fetchOne();
			}
			else {
				$Result = $Qevents->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}
			self::$EventsCache[$eventName] = ($Result && sizeof($Result) > 0 ? $Result : false);
		}

		return self::$EventsCache[$eventName];
	}

	public static function getGates($gateName = false)
	{
		if (!isset(self::$GatesCache[$eventName])){
			$Qgates = Doctrine_Query::create()
				->from('PayPerRentalGates');

			if ($gateName !== false){
				$Qgates->where('gate_name = ?', $gateName);
				$Result = $Qgates->fetchOne();
			}
			else {
				$Result = $Qgates->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}
			self::$GatesCache[$gateName] = ($Result && sizeof($Result) > 0 ? $Result : false);
		}

		return self::$GatesCache[$gateName];
	}

	public static function getRentalTypes($typeId = null)
	{
		if (empty(self::$RentalTypesCache[$typeId])){
			$Query = Doctrine_Query::create()
				->from('PayPerRentalTypes');

			if ($typeId !== null){
				$Query->where('pay_per_rental_types_id = ?', $typeId);
			}

			$Result = $Query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			self::$RentalTypesCache[$typeId] = ($Result && sizeof($Result) > 0 ? $Result : false);
		}
		return self::$RentalTypesCache[$typeId];
	}

	public static function getRentalPricing($PayPerRentalId)
	{
		if (!isset(self::$RentalPricingCache[$PayPerRentalId])){
			$QPricePerRentalProducts = Doctrine_Query::create()
				->from('PricePerRentalPerProducts p')
				->leftJoin('p.Description d')
				->leftJoin('p.Type t')
				->where('p.pay_per_rental_id = ?', $PayPerRentalId)
				->andWhere('d.language_id = ?', Session::get('languages_id'))
				->orderBy('p.price_per_rental_per_products_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			self::$RentalPricingCache[$PayPerRentalId] = $QPricePerRentalProducts;
		}
		return self::$RentalPricingCache[$PayPerRentalId];
	}

	/**
	 * Filter through a price array to determine which is the lowest for the reservation
	 *
	 * @static
	 * @param array    $Prices
	 * @param DateTime $StartDate
	 * @param DateTime $EndDate
	 * @return mixed
	 */
	public static function getLowestPrice($Prices, DateTime $StartDate, DateTime $EndDate)
	{
		$NumberOfMinutes = (($EndDate->diff($StartDate)->days * SesDateTime::TIME_DAY) / SesDateTime::TIME_MINUTE);

		$BestPrice = $Prices[0];
		foreach($Prices as $PriceInfo){
			//echo $PriceInfo['price'] . ' / ' . ($PriceInfo['Type']['minutes'] * $PriceInfo['number_of']) . "<br>\n";
			$PricePerMinute = ($PriceInfo['price'] / ($PriceInfo['Type']['minutes'] * $PriceInfo['number_of']));
			$TotalPrice = $PricePerMinute * $NumberOfMinutes;
			if ($TotalPrice < $BestPrice['price']){
				$BestPrice = $PriceInfo;
			}
		}
		return $BestPrice;
	}

	public static function getPricingPeriodInfo($PayPerRentalId, DateTime $StartDate, DateTime $EndDate)
	{
		$NumberOfMinutes = (($EndDate->diff($StartDate)->days * SesDateTime::TIME_DAY) / SesDateTime::TIME_MINUTE);

		$return = array();
		foreach(self::getRentalPricing($PayPerRentalId) as $PricingInfo){
			$CheckMinutes = $PricingInfo['number_of'] * $PricingInfo['Type']['minutes'];
			if ($CheckMinutes >= $NumberOfMinutes){
				$return['current'] = $PricingInfo;
				break;
			}
			$return['previous'] = $PricingInfo;
		}
		return $return;
	}

	public function discountPrice($price, $PricingInfo, $Discounts, $ReservationInfo)
	{
		global $Editor, $appExtension;
		$checkStoreId = 0;
		if ($appExtension->isEnabled('multiStore')){
			if (isset($ReservationInfo['store_id'])){
				$checkStoreId = $ReservationInfo['store_id'];
			}
			elseif (isset($Editor) && $Editor->hasData('store_id')) {
				$checkStoreId = $Editor->getData('store_id');
			}
			elseif (Session::exists('current_store_id')) {
				$checkStoreId = Session::exists('current_store_id');
			}
		}

		$discount = false;
		if (isset($Discounts[$checkStoreId])){
			$NumberOfMinutes = (($ReservationInfo['end_date']->diff($ReservationInfo['start_date'])->days * SesDateTime::TIME_DAY) / SesDateTime::TIME_MINUTE);
			foreach($Discounts[$checkStoreId] as $dInfo){
				if ($dInfo['ppr_type'] == $PricingInfo['pay_per_rental_types_id']){
					$checkFrom = $dInfo['discount_from'] * $PricingInfo['Type']['minutes'];
					$checkTo = $dInfo['discount_to'] * $PricingInfo['Type']['minutes'];
					if ($NumberOfMinutes >= $checkFrom && $NumberOfMinutes <= $checkTo){
						if ($dInfo['discount_type'] == 'percent'){
							$discount = ($price * ($dInfo['discount_amount'] / 100));
						}
						else {
							$discount = $dInfo['discount_amount'];
						}
					}
				}
			}

			if ($discount !== false){
				$price -= $discount;
			}
		}
		return $price;
	}

	public static function remove_item_by_value($array, $val = '', $preserve_keys = true)
	{
		if (empty($array) || !is_array($array)){
			return false;
		}
		if (!in_array($val, $array)){
			return $array;
		}

		foreach($array as $key => $value){
			if ($value == $val){
				unset($array[$key]);
			}
		}

		return ($preserve_keys === true) ? $array : array_values($array);
	}

	public static function onSaveSale(array $ReservationInfo, PurchaseType_reservation $PurchaseType, OrderProduct $OrderProduct, AccountsReceivableSalesProducts $SaleProduct, $AssignInventory)
	{
		global $appExtension, $_excludedBarcodes, $_excludedQuantities;
		$trackMethod = $PurchaseType->getTrackMethod();
		$infoPages = $appExtension->getExtension('infoPages');
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SAVE_TERMS') == 'True'){
			$termInfoPage = $infoPages->getInfoPage('conditions');
		}
		for($count = 0; $count < $OrderProduct->getQuantity(); $count++){
			$Reservation = $SaleProduct->Reservations->getTable()->getRecord();
			$Reservation->products_id = $OrderProduct->getProductsId();
			$Reservation->start_date = $ReservationInfo['start_date']->format(DATE_TIMESTAMP);
			$Reservation->end_date = $ReservationInfo['end_date']->format(DATE_TIMESTAMP);
			$Reservation->insurance = (isset($ReservationInfo['insurance']) ? $ReservationInfo['insurance'] : 0);
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
				$Reservation->event_name = $ReservationInfo['event_name'];
				$Reservation->event_date = $ReservationInfo['event_date']->format(DATE_TIMESTAMP);
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
					$Reservation->event_gate = $ReservationInfo['event_gate'];
				}
			}
			$Reservation->semester_name = $ReservationInfo['semester_name'];
			$Reservation->rental_state = 'reserved';
			if (isset($ReservationInfo['shipping']['id']) && !empty($ReservationInfo['shipping']['id'])){
				$Reservation->shipping_method_title = $ReservationInfo['shipping']['title'];
				$Reservation->shipping_method = $ReservationInfo['shipping']['id'];
				$Reservation->shipping_days_before = $ReservationInfo['shipping']['days_before'];
				$Reservation->shipping_days_after = $ReservationInfo['shipping']['days_after'];
				$Reservation->shipping_cost = $ReservationInfo['shipping']['cost'];
			}

			if (isset($termInfoPage)){
				$Reservation->rental_terms = str_replace("\r", '', str_replace("\n", '', str_replace("\r\n", '', $termInfoPage['PagesDescription'][Session::get('languages_id')]['pages_html_text'])));
				if (sysConfig::get('TERMS_INITIALS') == 'true' && Session::exists('agreed_terms')){
					$Reservation->rental_terms .= '<br/>Initials: ' . Session::get('agreed_terms');
				}
			}

			EventManager::notify('ReservationOnInsertOrderedProduct', $Reservation, $OrderProduct);
			$SaleProduct->Reservations->add($Reservation);

			if ($AssignInventory === true){
				$Inventory = $SaleProduct->SaleInventory->getTable()->getRecord();
				if ($trackMethod == 'barcode'){
					$Inventory->barcode_id = $PurchaseType->getAvailableBarcode($ReservationInfo, $_excludedBarcodes);
					$Inventory->Barcode->status = 'R';
					$_excludedBarcodes[] = $Inventory->barcode_id;
				}
				elseif ($trackMethod == 'quantity') {
					$Inventory->quantity_id = $PurchaseType->getAvailableQuantity($ReservationInfo, $_excludedQuantities);
					$Inventory->Quantity->available -= 1;
					$Inventory->Quantity->reserved += 1;
					$_excludedQuantities[] = $Inventory->quantity_id;
				}
				$SaleProduct->SaleInventory->add($Inventory);
			}
		}
	}

	public static function parse_reservation_info(array $ReservationData)
	{
		global $currencies;
		$return = '';
		$return .= '<br /><small><b><i><u>' . sysLanguage::get('TEXT_INFO_RESERVATION_INFO') . '</u></i></b></small>';

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
			if (!isset($ReservationData['semester_name']) || $ReservationData['semester_name'] == ''){
				if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_HOURLY') == 'True'){
					$DateFormat = 'getDateTimeFormat';
				}
				else {
					$DateFormat = 'getDateFormat';
				}

				$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_START_DATE') . ' ' . $ReservationData['start_date']->format(sysLanguage::$DateFormat('long')) . '</i></small>' .
				'<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_END_DATE') . ' ' . $ReservationData['end_date']->format(sysLanguage::$DateFormat('long')) . '</i></small>';
			}
			else {
				$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_SEMESTER') . ' ' . $ReservationData['semester_name'] . '</i></small>';
			}
		}
		else {
			$return .= '<br /><small><i> - Event Date: ' . $ReservationData['start_date']->format(sysLanguage::getDateTimeFormat('long')) . '</i></small>' .
			'<br /><small><i> - Event Name: ' . $ReservationData['event_name'] . '</i></small>';
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$return .= '<br /><small><i> - Event Gate: ' . $ReservationData['event_gate'] . '</i></small>';
			}
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_SHIPPING') == 'True' && isset($ReservationData['shipping']) && $ReservationData['shipping'] !== false && isset($ReservationData['shipping']['title']) && !empty($ReservationData['shipping']['title']) && isset($ReservationData['shipping']['cost'])){
			if ($ReservationData['shipping']['cost'] > 0){
				$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_SHIPPING_METHOD') . ' ' . $ReservationData['shipping']['title'] . ' (' . $currencies->format($ReservationData['shipping']['cost']) . ')</i></small>';
			}
			else {
				$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_SHIPPING_METHOD') . ' ' . $ReservationData['shipping']['title'] . ' (' . 'Free Shipping' . ')</i></small>';
			}
		}

		if (isset($ReservationData['deposit_amount']) && $ReservationData['deposit_amount'] > 0){
			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_DEPOSIT_AMOUNT') . ' ' . $currencies->format($ReservationData['deposit_amount']) . '</i></small>';
		}
		if (isset($ReservationData['insurance']) && $ReservationData['insurance'] > 0){
			$return .= '<br /><small><i> - ' . sysLanguage::get('TEXT_INFO_INSURANCE') . ' ' . $currencies->format($ReservationData['insurance']) . '</i></small>';
		}
		//$return .= '<br />';
		EventManager::notify('ParseReservationInfo', &$return, &$ReservationData);
		return $return;
	}
}
