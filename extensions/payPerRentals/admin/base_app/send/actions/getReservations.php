<?php
	$html = '';
$Reservations = Doctrine_Query::create()
	->from('PayPerRentalReservations')
	->where('start_date BETWEEN ? AND ?', array($_GET['start_date'], $_GET['end_date']))
	->andWhere('rental_state = ?', 'reserved')
	->execute();
	/*$Qreservations = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersAddresses oa')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation opr')
	->leftJoin('opr.ProductsInventoryBarcodes ib')
	->leftJoin('ib.ProductsInventory i')
	->leftJoin('opr.ProductsInventoryQuantity iq')
	->leftJoin('iq.ProductsInventory i2')
	->where('opr.start_date BETWEEN "' . $_GET['start_date'] . '" AND "' . $_GET['end_date'] . '"')
	->andWhere('opr.rental_state = ?', 'reserved')
	->andWhere('oa.address_type = ?', 'delivery')
	->andWhere('opr.parent_id is null');

	if(isset($_GET['eventSort'])){
		$Qreservations->orderBy('opr.event_name ' . $_GET['eventSort']);
	}
	if(isset($_GET['gateSort'])){
		$Qreservations->orderBy('opr.event_gate ' . $_GET['gateSort']);
	}

	if ($_GET['filter_pay'] == 'pay'){
		$Qreservations->andWhere('opr.amount_payed >= op.final_price');
	}else
	if ($_GET['filter_pay'] == 'notpay'){
		$Qreservations->andWhere('opr.amount_payed < op.final_price');
	}
	if ((int)$_GET['filter_status'] > 0){
		if($_GET['filter_status'] == 2){
			$Qreservations->andWhere('opr.rental_status_id = '. $_GET['filter_status'].' OR opr.rental_status_id is null');
		}else{
			$Qreservations->andWhere('opr.rental_status_id = ?', $_GET['filter_status']);
		}
	}
*/

EventManager::notify('OrdersListingBeforeExecute', &$Qreservations);

	$Qreservations = $Qreservations->execute();
	if ($Qreservations !== false){
		$Orders = $Qreservations->toArray(true);

		$Reservations = array();
		foreach($Orders as $oInfo){
			$Reservation = array(
				'orders_id'       => $oInfo['orders_id'],
				'OrdersAddresses' => $oInfo['OrdersAddresses'],
				'OrdersProducts'  => $oInfo['OrdersProducts']
			);

			$Reservations[] = $Reservation;
		}
		EventManager::notify('GetReservationsOrdersListingPopulateArray', &$Reservations);

		foreach($Reservations as $oInfo){
			$html .= '<tr class="dataTableRow"><td colspan="11" style="border-bottom:1px solid #000000;">Order id:'.$oInfo['orders_id'].' </td></tr>';
			foreach($oInfo['OrdersProducts'] as $opInfo){
				foreach($opInfo['OrdersProductsReservation'] as $rInfo){
					$orderAddress = $oInfo['OrdersAddresses']['delivery'];

					$orderId = $oInfo['orders_id'];
					$productName = $opInfo['products_name'];

					$customersName = $orderAddress['entry_name'];
					$trackMethod = $rInfo['track_method'];
					$useCenter = 0;

					$resStart = $rInfo['start_date']->format(sysLanguage::getDateTimeFormat());
					$resEnd = $rInfo['end_date']->format(sysLanguage::getDateTimeFormat());

					$padding_days_before = $rInfo['shipping_days_before'];
					$padding_days_after = $rInfo['shipping_days_after'];

					$shipOnDate = $rInfo['start_date'];
					$shipOnDate->modify('-' . $padding_days_before . ' Day');
					$shipOn = $shipOnDate->format(sysLanguage::getDateFormat('short'));

					$dueBackDate = $rInfo['end_date'];
					$dueBackDate->modify('+' . $padding_days_after . ' Day');
					$dueBack = $dueBackDate->format(sysLanguage::getDateFormat('short'));

					$Qcheck = Doctrine_Query::create()
					->from('OrdersProductsReservation opr')
					->leftJoin('opr.ProductsInventoryBarcodes ib')
					->leftJoin('ib.ProductsInventory i')
					->leftJoin('opr.ProductsInventoryQuantity iq')
					->leftJoin('iq.ProductsInventory i2')
					->where('parent_id = ?', $rInfo['orders_products_reservations_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$inventoryCenterName = "Default Store";
					if ($trackMethod == 'barcode'){
						$barcodeId = $rInfo['ProductsInventoryBarcodes']['barcode_id'];
						$barcodeNum = $rInfo['ProductsInventoryBarcodes']['barcode'];
						if ($appExtension->isInstalled('inventoryCenters') && isset($rInfo['ProductsInventoryBarcodes']['ProductsInventory'])){
							$useCenter = $rInfo['ProductsInventoryBarcodes']['ProductsInventory']['use_center'];
						}else{
							$useCenter = 0;
						}
						if ($Qcheck){
							$barcodeNum = array();
							foreach($Qcheck as $rInfo2){
								$barcodeNum[] = $rInfo2['ProductsInventoryBarcodes']['barcode'];
							}
							if (sizeof($barcodeNum) > 0){
								$barcodeNum = implode(', ', $barcodeNum);
							}
						}

						if ($appExtension->isInstalled('inventoryCenters') && $useCenter == '1'){
							//todo check for inventory centers where products barcodes resides or multistore

							if($rInfo['inventory_center_pickup'] != 0){
								$invCenter = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($rInfo['inventory_center_pickup']);
								$inventoryCenterName = $invCenter->inventory_center_name;
							}else{
								//depending of the stock method store or inv center
								$invext = $appExtension->getExtension('inventoryCenters');

								if ($invext->stockMethod == 'Store'){
									$Qinvbs = Doctrine_Query::create()
														->from('ProductsInventoryBarcodesToStores b2s')
														->leftJoin('b2s.Stores s')
														->where('b2s.barcode_id = ?',$barcodeId)
														->fetchOne();
									if($Qinvbs)
										$inventoryCenterName = $Qinvbs->Stores->stores_name;

								}else{
									$Qinvbs = Doctrine_Query::create()
														->from('ProductsInventoryBarcodesToInventoryCenters b2c')
														->leftJoin('b2c.ProductsInventoryCenters ic')
														->where('b2c.barcode_id = ?',$barcodeId)
														->fetchOne();
									if($Qinvbs)
										$inventoryCenterName = $Qinvbs->ProductsInventoryCenters->inventory_center_name;

								}

							}
						}
					}else{

						$quantityId = $rInfo['ProductsInventoryQuantity']['quantity_id'];

						$useCenter = isset($rInfo['ProductsInventoryQuantity']['ProductsInventory']['use_center'])?$rInfo['ProductsInventoryQuantity']['ProductsInventory']['use_center']:'0';

						$barcodeNum = 'Quantity Tracking';
						if ($Qcheck){
							$barcodeNum .= ' ( Package )';
						}

							if ($appExtension->isInstalled('inventoryCenters') && $useCenter == '1'){

							//todo check for inventory centers where products barcodes resides or multistore
							//get inventory center name for inventory_center_pickup
							if($rInfo['inventory_center_pickup'] != 0){
								$invCenter = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($rInfo['inventory_center_pickup']);
								$inventoryCenterName = $invCenter->inventory_center_name;
							}else{
								//todo check getResservation for quantity tracking

								$invext = $appExtension->getExtension('inventoryCenter');
								if ($invext->stockMethod == 'Store'){
									$Qinvbs = Doctrine_Query::create()
														->from('ProductsInventoryQuantity b2s')
														->where('b2s.quantity_id = ?',$quantityId)
														->fetchOne();
									if($Qinvbs){
										$Store = Doctrine_Core::getTable('Stores')->findOneByStoreId($Qinvbs->inventory_store_id);
										$inventoryCenterName = $Store->stores_name;
									}

								}else{
									$Qinvbs = Doctrine_Query::create()
											->from('ProductsInventoryQuantity b2s')
											->where('b2s.quantity_id = ?',$quantityId)
											->fetchOne();
									if($Qinvbs){
											$invCent = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($Qinvbs->inventory_center_id);
											$inventoryCenterName = $invCent->inventory_center_name;
									}


								}
							}
						}

					}
					$trackNumber = '';
					if(!empty($oInfo['usps_track_num'])){
						$trackNumber = $oInfo['usps_track_num'];
					}
					if(!empty($oInfo['usps_track_num2'])){
						$trackNumber = $oInfo['usps_track_num2'];
					}
					if(!empty($oInfo['ups_track_num'])){
						$trackNumber = $oInfo['ups_track_num'];
					}
					if(!empty($oInfo['ups_track_num2'])){
						$trackNumber = $oInfo['ups_track_num2'];
					}
					if(!empty($oInfo['fedex_track_num'])){
						$trackNumber = $oInfo['fedex_track_num'];
					}
					if(!empty($oInfo['fedex_track_num2'])){
						$trackNumber = $oInfo['fedex_track_num2'];
					}
					if(!empty($oInfo['dhl_track_num'])){
						$trackNumber = $oInfo['dhl_track_num'];
					}
					if(!empty($oInfo['dhl_track_num2'])){
						$trackNumber = $oInfo['dhl_track_num2'];
					}
					$payedAmount =

					$shippingTrackingNumber = htmlBase::newElement('input')
					->setName('shipping_number['.$rInfo['orders_products_reservations_id'].']')
					->setValue($trackNumber);
					$statusSelect = '<select name="rental_status['.$rInfo['orders_products_reservations_id'].']">';
					$QrentalStatus = Doctrine_Query::create()
						->from('RentalStatus')
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					foreach($QrentalStatus as $iStatus){
						if(is_null($rInfo['rental_status_id']) && $iStatus['rental_status_id'] == '2'){
							$statusSelect.= '<option selected="selected" value="'.$iStatus['rental_status_id'].'">'.$iStatus['rental_status_text'].'</option>';
						}elseif ($rInfo['rental_status_id'] == $iStatus['rental_status_id']){
							$statusSelect.= '<option selected="selected" value="'.$iStatus['rental_status_id'].'">'.$iStatus['rental_status_text'].'</option>';
						}else{
							$statusSelect.= '<option value="'.$iStatus['rental_status_id'].'">'.$iStatus['rental_status_text'].'</option>';
						}
					}

					$statusSelect .= '</select>';

					if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
						$payedAmount = $opInfo['final_price'];

						$payAmount = htmlBase::newElement('input')
						->setName('amount_payed['.$rInfo['orders_products_reservations_id'].']');

						if($rInfo['amount_payed'] > 0){
							$payAmount->setLabel('Payed('.$currencies->format($rInfo['amount_payed']).')')
							->setLabelPosition('after');
							$payedAmount -= $rInfo['amount_payed'];
						}

						$payAmount->setValue($payedAmount);
					}

					$barcodeReplacement = htmlBase::newElement('input')
					->setName('barcode_replacement['.$rInfo['orders_products_reservations_id'].']')
					->attr('resid', $rInfo['orders_products_reservations_id'])
					//->attr('readonly','readonly')
					->addClass('barcodeReplacement');
					$style = '';
					$selVal = '';
					if(isset($_GET['highlightOID']) && $_GET['highlightOID'] == $orderId){
						$style = 'style="background-color:red;color:white"';
						$selVal = 'checked="checked"';
					}

					$html .= '<tr class="dataTableRow" '.$style.'>' .
						'<td><input type="checkbox" name="sendRes[]" class="reservations"'.$selVal.' value="' . $rInfo['orders_products_reservations_id'] . '"></td>' .
						'<td class="dataTableContent">' . $customersName . '</td>' .
						'<td class="dataTableContent">' . $productName . '</td>' .
						'<td class="dataTableContent">' . $barcodeNum . '</td>' .
						'<td class="dataTableContent">' . $barcodeReplacement->draw() . '</td>';
					if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
						$html .=  '<td class="dataTableContent">' . $rInfo['event_name'] . '</td>';
						if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GATES') == 'True'){
							$html .=  '<td class="dataTableContent">' . $rInfo['event_gate'] . '</td>';
						}
					}
					$html .= '<td class="dataTableContent" align="center"><table cellpadding="2" cellspacing="0" border="0">' .
							'<tr>' .
								'<td class="dataTableContent">Ship On: </td>' .
								'<td class="dataTableContent">' . $shipOn . '</td>' .
							'</tr>' .
							'<tr>' .
								'<td class="dataTableContent">Res Start: </td>' .
								'<td class="dataTableContent">' . $resStart . '</td>' .
							'</tr>' .
							'<tr>' .
								'<td class="dataTableContent">Res End: </td>' .
								'<td class="dataTableContent">' . $resEnd . '</td>' .
							'</tr>' .
							'<tr>' .
								'<td class="dataTableContent">Due Back: </td>' .
								'<td class="dataTableContent">' . $dueBack . '</td>' .
							'</tr>' .

						'</table></td>' .
						'<td class="dataTableContent">' . $inventoryCenterName . '</td>';
						if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_TRACKING_NUMBER_COLUMN') == 'True'){
							$html .= '<td class="dataTableContent">' . $shippingTrackingNumber->draw() . '</td>';
						}
						if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_RESERVATION_STATUS_COLUMN') == 'True'){
							$html .= '<td class="dataTableContent">' . $statusSelect . '</td>';
						}
						if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_PROCESS_SEND') == 'True'){
							$html .= '<td class="dataTableContent">' . $payAmount->draw() . '</td>' ;
						}
						$html .='<td class="dataTableContent" align="center"><a href="' . itw_app_link('oID=' . $orderId, 'orders', 'details') . '">View Order</a></td>' .
					'</tr>';
				}
			}
		}
	}
	
	EventManager::attachActionResponse($html, 'html');
?>