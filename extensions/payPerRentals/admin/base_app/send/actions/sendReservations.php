<?php


foreach($_POST['barcode_replacement'] as $resId => $barcode){
		$Qres = Doctrine_Core::getTable('OrdersProductsReservation')->find($resId);
		$QBarcode = Doctrine_Query::create()
		->from('ProductsInventoryBarcodes')
		->andWhere('barcode = ?', $barcode)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if($QBarcode){
			$Qres->barcode_id = $QBarcode[0]['barcode_id'];
			$Qres->save();
		}
}

$Qreservations = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.Customers c')
	->leftJoin('o.OrdersAddresses oa')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->leftJoin('ib.ProductsInventory i')
	->leftJoin('opr.ProductsInventoryQuantity iq')
	->leftJoin('iq.ProductsInventory i2')
	->whereIn('opr.orders_products_reservations_id', (isset($_POST['sendRes'])?$_POST['sendRes']:array()))
	->andWhere('oa.address_type = ?', 'customer')
	->andWhere('opr.parent_id IS NULL')
	->execute();
    $Arr = array();
	if ($Qreservations->count() > 0){
		foreach($Qreservations as $oInfo){

			foreach($oInfo->OrdersProducts as $opInfo){
				foreach($opInfo->OrdersProductsReservation as $oprInfo){

					if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_MAINTENANCE') == 'True'){
						//check if it has prehire maintenance. if yes then check current_maintenance_type to be 0 if not then cannot be sended
						$QMaintenancePeriod = Doctrine_Query::create()
							->from('PayPerRentalMaintenancePeriods ppmp')
							->where('before_send = ?', '1')
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						if(count($QMaintenancePeriod) > 0){
							$BarcodeHistoryRented = Doctrine_Core::getTable('BarcodeHistoryRented')->findOneByBarcodeId($oprInfo->barcode_id);

							if(!$BarcodeHistoryRented || $BarcodeHistoryRented->current_maintenance_type != 0){
								continue;
							}
						}
					}

					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
						$payedAmount = (isset($_POST['amount_payed'][$oprInfo->orders_products_reservations_id])?$_POST['amount_payed'][$oprInfo->orders_products_reservations_id]:'');

						if($payedAmount - $opInfo['final_price'] >= 0){

						}else{
							continue;
						}
					}

					$Arr[] = $oprInfo->orders_products_reservations_id;

					$shippingNumber = (isset($_POST['shipping_number'][$oprInfo->orders_products_reservations_id])?$_POST['shipping_number'][$oprInfo->orders_products_reservations_id]:'');
					if(!empty($shippingNumber)){
						if(preg_match('/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|[\dT]\d\d\d ?\d\d\d\d ?\d\d\d)\b/i', $shippingNumber)){
							$shippingURL = '<a href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package&InquiryNumber1='.$shippingNumber.'">Track Order</a>';//UPS
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'UPS';
							//$oInfo->ups_track_num = $shippingNumber;
						}elseif(preg_match('/\b((96\d\d\d\d\d ?\d\d\d\d|96\d\d) ?\d\d\d\d ?d\d\d\d( ?\d\d\d)?)\b/i', $shippingNumber)){
							$shippingURL = '<a href="http://www.fedex.com/Tracking?action=track&language=english&cntry_code=us&tracknumbers='.$shippingNumber.'">Track Order</a>';//FEDEX
							//$oInfo->fedex_track_num = $shippingNumber;
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'FEDEX';
						}elseif(preg_match('/\b(91\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d|91\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d ?\d\d\d\d)\b/i', $shippingNumber)){
							$shippingURL = '<a href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum='.$shippingNumber.'">Track Order</a>';//USPS
							//$oInfo->usps_track_num = $shippingNumber;
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'USPS';
						}else{
							$shippingURL = '<a href="http://track.dhl-usa.com/atrknav.asp?action=track&language=english&cntry_code=us&ShipmentNumber='.$shippingNumber.'">Track Order</a>';//DHL-starts with JD
							//$oInfo->dhl_track_num = $shippingNumber;
							$oprInfo->tracking_number = $shippingNumber;
							$oprInfo->tracking_type = 'DHL';
						}
					}

					$oprInfo->rental_state = 'out';
					$oprInfo->date_shipped = date('Y-m-d');

					if ($oprInfo->track_method == 'barcode'){
						$oprInfo->ProductsInventoryBarcodes->status = 'O';
					}elseif ($oprInfo->track_method == 'quantity'){
						$oprInfo->ProductsInventoryQuantity->reserved -= 1;
						$oprInfo->ProductsInventoryQuantity->qty_out += 1;
					}
					if (isset($oInfo->Customers)){
						$emailEvent = new emailEvent('reservation_sent', $oInfo->Customers->language_id);

						$emailEvent->setVars(array(
							'full_name' => $oInfo->OrdersAddresses['customer']->entry_name,
							'rented_product' => $opInfo->products_name,
							'due_date' => tep_date_long($oprInfo->end_date),
	 						'shipping_number'=> (isset($shippingURL)?$shippingURL:''),
							'email_address' => $oInfo->customers_email_address
						));

						$emailEvent->sendEmail(array(
							'email' => $oInfo->customers_email_address,
							'name' => $oInfo->OrdersAddresses['customer']->entry_name
						));
					}
					$oprInfo->save();
				}

			}
		}
		$Qreservations->save();
	}



	EventManager::attachActionResponse(array(
		'success' => true,
		'Arr' => $Arr
	), 'json');
?>