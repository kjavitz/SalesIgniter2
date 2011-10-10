<?php
if (!class_exists('PurchaseType_reservation')){
	require(sysConfig::getDirFsCatalog() . 'extensions/payPerRentals/purchaseTypeModules/reservation/module.php');
}

class OrderCreatorProductPurchaseTypeReservation extends PurchaseType_reservation {
	
	public function addToOrdersProductCollection(&$ProductObj, &$CollectionObj){
		global $Editor;
		$ResInfo = $ProductObj->getInfo('reservationInfo');
		$allInfo = $ProductObj->getPInfo();
		if (isset($allInfo['aID_string']) && !empty($allInfo['aID_string'])){
			$this->inventoryCls->invMethod->trackMethod->aID_string = $allInfo['aID_string'];
		}
		$ShippingInfo = $ResInfo['shipping'];

		$StartDateArr = date_parse($ResInfo['start_date']);
		$EndDateArr = date_parse($ResInfo['end_date']);
		$StartDateFormatted = $this->formatDateArr('Y-m-d H:i:s', $StartDateArr);
		$EndDateFormatted = $this->formatDateArr('Y-m-d H:i:s', $EndDateArr);
		$Insurance = (isset($ResInfo['insurance']) ? $ResInfo['insurance'] : 0);
		$TrackMethod = $this->inventoryCls->getTrackMethod();

		$EventName ='';
		$EventDate = '0000-00-00 00:00:00';
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$EventName = $ResInfo['event_name'];
			$EventDate = $ResInfo['event_date'];
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$EventGate = $ResInfo['event_gate'];
			}

		}

		$Reservations =& $CollectionObj->OrdersProductsReservation;
		$existingInfo = $ProductObj->getInfo();
		$QexitingOrders = Doctrine::getTable('OrdersProducts')->find($existingInfo['orders_products_id']);
		if ($QexitingOrders){
			$QexitingOrders->OrdersProductsReservation->delete();
		}

		$excludedBarcode = array();
		$excludedQuantity = array();
		for($count=1; $count <= $ResInfo['quantity']; $count++){
			$Reservation = new OrdersProductsReservation();
			$Reservation->start_date = $StartDateFormatted;
			$Reservation->end_date = $EndDateFormatted;
			$Reservation->insurance = $Insurance;
			$Reservation->event_name = $EventName;
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				$Reservation->event_gate = $EventGate;
			}
			$Reservation->event_date = $EventDate;
			$Reservation->track_method = $TrackMethod;
			$Reservation->rental_state = 'reserved';
			if(isset($_POST['estimateOrder'])){
				$Reservation->is_estimate = 1;
			}else{
				$Reservation->is_estimate = 0;
			}
			if (isset($ShippingInfo['id']) && !empty($ShippingInfo['id'])){
				$Reservation->shipping_method_title = $ShippingInfo['title'];
				$Reservation->shipping_method = $ShippingInfo['id'];
				$Reservation->shipping_days_before = $ShippingInfo['days_before'];
				$Reservation->shipping_days_after = $ShippingInfo['days_after'];
				$Reservation->shipping_cost = $ShippingInfo['cost'];
			}
			if(!isset($_POST['estimateOrder'])){
				if ($TrackMethod == 'barcode'){
					$barId = $this->getAvailableBarcode($ProductObj, $excludedBarcode, $allInfo['usableBarcodes']);
					if($barId != -1){
						$Reservation->barcode_id = $barId;
					}else{
						$Editor->addErrorMessage('Reservation already taken for the date. Please reselect');
					}
					$excludedBarcode[] = $Reservation->barcode_id;
					$Reservation->ProductsInventoryBarcodes->status = 'R';
				}elseif ($TrackMethod == 'quantity'){
					$qtyId = $this->getAvailableQuantity($ProductObj, $excludedQuantity);
					if($qtyId != -1){
						$Reservation->quantity_id = $qtyId;
					}else{
						$Editor->addErrorMessage('Reservation already taken for the date. Please reselect');
					}
					$excludedQuantity[] = $Reservation->quantity_id;
					$Reservation->ProductsInventoryQuantity->available -= 1;
					$Reservation->ProductsInventoryQuantity->reserved += 1;
				}
			}
			EventManager::notify('ReservationOnInsertOrderedProduct', $Reservation, &$ProductObj);

			$Reservations->add($Reservation);
		}
	}
	public function getBookedDaysArrayNew($starting, $qty, &$reservArr, &$bookedDates, $newReservations){
		$reservArr = ReservationUtilities::getMyReservations(
			$this->productInfo['id'],
			$starting,
			$this->overBookingAllowed()
		);
		/*
		foreach($newReservations as $reservationProductAll){
			$reservationProduct = $reservationProductAll->getInfo();
			if (isset($reservationProduct['OrdersProductsReservation'])){

				foreach($reservationProduct['OrdersProductsReservation'] as $iReservation){
					$reservationArr = array();

					$startDateArr = date_parse($iReservation['start_date']);
					$endDateArr = date_parse($iReservation['end_date']);

					$startTime = mktime($startDateArr['hour'],$startDateArr['minute'],$startDateArr['second'],$startDateArr['month'],$startDateArr['day']-$iReservation['shipping_days_before'],$startDateArr['year']);
					$endTime = mktime($endDateArr['hour'],$endDateArr['minute'],$endDateArr['second'],$endDateArr['month'],$endDateArr['day']+$iReservation['shipping_days_after'],$endDateArr['year']);

					$dateStart = date('Y-n-j', $startTime);
					$timeStart = date('G:i', $startTime);

					$dateEnd = date('Y-n-j', $endTime);
					$timeEnd = date('G:i', $endTime);

					if($timeStart == '0:00'){
						$reservationArr['start'] = $dateStart;
					}else{
						$reservationArr['start_time'] = $timeStart;
						$reservationArr['start_date'] = $dateStart;
						$reservationArr['end_time'] = '23:59';
						$reservationArr['end_date'] = $dateStart;
						$nextStartTime = strtotime('+1 day', strtotime($dateStart));
						$prevEndTime = strtotime('-1 day', strtotime($dateEnd));
						if( $nextStartTime <= $prevEndTime){
							$reservationArr['start'] = date('Y-n-j', $nextStartTime);
						}
					}

					if($timeEnd == '0:00'){
						$reservationArr['end'] = $dateEnd;
					}else{
						if(!isset($reservationArr['start_time'])){
							$reservationArr['start_time'] = '0:00';
						}
						$reservationArr['start_date'] = $dateEnd;
						$reservationArr['end_time'] = $timeEnd;
						$reservationArr['end_date'] = $dateEnd;
						$nextStartTime = strtotime('+1 day', strtotime($dateStart));
						$prevEndTime = strtotime('-1 day', strtotime($dateEnd));
						if( $nextStartTime <= $prevEndTime){
							$reservationArr['end'] = date('Y-n-j', $prevEndTime);
						}
					}

				    $reservationArr['barcode'] = $iReservation['barcode_id'];//if barcode_id is null or 0 this means is quantity and check will be made with the total qty at some point.
					$reservationArr['qty'] = 1;

					$reservArr[] = $reservationArr;
				}
			}

		}*/

		//$bookedDates = array();
		foreach($reservArr as $iReservation){
			if(isset($iReservation['start']) && isset($iReservation['end'])){
				$startTime = strtotime($iReservation['start']);
				$endTime = strtotime($iReservation['end']);
				while($startTime<=$endTime){
					$dateFormated = date('Y-n-j', $startTime);
					if (isset($bookedDates[$dateFormated]['barcode']) && !is_null($bookedDates[$dateFormated]['barcode']) && $this->getTrackMethod() == 'barcode' && !in_array($iReservation['barcode'], $bookedDates[$dateFormated]['barcode'])){
						$bookedDates[$dateFormated]['barcode'][] = $iReservation['barcode'];
						//check if all the barcodes are already or make a new function to make checks by qty... (this function can return also the free barcode?)
					}
					else {
						if (isset($bookedDates[$dateFormated]['qty'])){
							$bookedDates[$dateFormated]['qty'] = $bookedDates[$dateFormated]['qty'] + 1;
						}
						else {
							$bookedDates[$dateFormated]['qty'] = 1;
						}
						//check if there is still qty available.
					}

					$startTime += 60*60*24;
				}
			}
		}
		$bookingsArr = array();
		$prodBarcodes = array();
		foreach($this->getProductsBarcodes() as $iBarcode){
			$prodBarcodes[] = $iBarcode['id'];
		}

		if(count($prodBarcodes) < $qty){
			return false;
		}else{
			foreach($bookedDates as $dateFormated => $iBook){
				if ($this->getTrackMethod() == 'barcode'){
					$myqty = 0;
					if(isset($iBook['barcode'])){
						foreach($iBook['barcode'] as $barcode){
							if(in_array($barcode,$prodBarcodes)){
								$myqty ++;
							}
						}
					}
					if(count($prodBarcodes) - $myqty<$qty){
						$bookingsArr[] = $dateFormated;
					}
				}else{
					if($prodBarcodes['available'] - $iBook['qty'] < $qty){
						$bookingsArr[] = $dateFormated;
					}
				}
			}
		}
		return $bookingsArr;
	}

	public function processAddToCartNew(&$pInfo, $resInfo){
		$shippingInfo = array(
			'',
			''
		);
		if (isset($resInfo['rental_shipping']) && $resInfo['rental_shipping'] !== false){
			$shippingInfo = explode('_', $resInfo['rental_shipping']);
		}
		$dataArray = array(
			'shipping_module' => (isset($shippingInfo[0])?$shippingInfo[0]:''),
			'shipping_method' => (isset($shippingInfo[1])?$shippingInfo[1]:''),
			'start_date'      => $resInfo['start_date'],
			'end_date'        => $resInfo['end_date'],
			'quantity'        => $resInfo['rental_qty']
		);
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
			$dataArray['event_name'] = $resInfo['event_name'];
			$dataArray['event_date'] = $resInfo['event_date'];
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
				if(isset($resInfo['event_gate'])){
					$dataArray['event_gate'] = $resInfo['event_gate'];
				}
			}
		}
		$this->processAddToOrderOrCart($dataArray, $pInfo);

		EventManager::notify('ReservationProcessAddToCart', $pInfo['reservationInfo']);
	}

}
?>