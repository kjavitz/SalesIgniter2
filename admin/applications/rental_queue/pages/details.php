<?php
$cID = $_GET['cID'];
$tableGrid = htmlBase::newElement('grid');

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text'  => 'Send',
			  'align' => 'center'
		),
		array('text' => sysLanguage::get('TABLE_HEADING_ID')),
		array('text' => sysLanguage::get('TABLE_HEADING_PRIORITY')),
		array('text' => sysLanguage::get('TABLE_HEADING_MOVIE_TITLE')),
		array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
		array('text' => sysLanguage::get('TABLE_HEADING_BAR_CODE')),
		array('text' => sysLanguage::get('TABLE_HEADING_BAR_IMG'))
	)
));

$todays_date = date('m/d/Y');
$rowCount = 0;
if ($rentalQueue->isEmpty()){
	$tableGrid->addBodyRow(array(
		'columns' => array(
			array(
				'text'  => 'Rental Queue for this customer is Empty',
				'attr'  => array(
					'colspan' => 7
				),
				'align' => 'center'
			)
		)
	));
}
else {
	$products = $rentalQueue->getProducts();
	for($i = 0, $n = sizeof($products); $i < $n; $i++){
		$selected = '';
		if (!isset($barcodeArray[$products[$i]['id']])){
			$purchaseTypeCls = $products[$i]['productClass']->getPurchaseType('rental');
			$productInv =& $purchaseTypeCls->invMethod->trackMethod;
			$productInv->invUnavailableStatus = array(
				'B',
				'O',
				'P',
				'R'
			);

			$invItems = $purchaseTypeCls->getInventoryItems();
			$barcodeArray[$products[$i]['id']] = array();

			if (!is_array($invItems) || sizeof($invItems) <= 0){
				$barcodeArray[$products[$i]['id']][] = array(
					'id'   => '',
					'text' => sysLanguage::get('TEXT_STOCK_OUT')
				);
			}
			else {
				foreach($invItems as $invItem){
					$text = $invItem['barcode'];
					if ($appExtension->isEnabled('inventoryCenters') === true && isset($invItem['center_id'])){
						if (!isset($selected) && $invItem['center_id'] == $QcustomerInvCenter->getVal('inventory_center_id')){
							$selected = $invItem['id'];
						}
						$text .= ' ( ' . $invItem['center_name'] . ' )';
					}
					$barcodeArray[$products[$i]['id']][] = array(
						'id'   => $invItem['id'],
						'text' => $text
					);
				}
			}
		}

		$QueueId = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select customers_queue_id from ' . TABLE_RENTAL_QUEUE . ' where customers_id = "' . $cID . '" and products_id = "' . $products[$i]['id'] . '"');

		$tableGrid->addBodyRow(array(
			'columns' => array(
				array('text'  => ($products[$i]['canSend'] === true ? tep_draw_checkbox_field('queueItem[]', $QueueId[0]['customers_queue_id']) : ''),
					  'align' => 'center'
				),
				array('text' => $products[$i]['id']),
				array('text'  => $products[$i]['priority'],
					  'align' => 'center'
				),
				array('text' => '<a href="' . tep_href_link(FILENAME_RENTAL_QUEUE_DETAILS, 'action=viewProduct&cID=' . $cID . '&pID=' . $products[$i]['id']) . '#page-4">' . $products[$i]['name'] . '</a>'),
				array('text' => $products[$i]['availability']),
				array('text' => tep_draw_pull_down_menu('barcode[' . $QueueId[0]['customers_queue_id'] . ']', $barcodeArray[$products[$i]['id']], $selected, 'class="barcodeMenu"')),
				array('text' => '<span id="barcodeImage"></span>')
			)
		));
	}
}

$totalRented = $rentalQueue->count_rented();
$totalCanSend = $membership->getRentalsAllowed() - $totalRented;

$infoTable = '<table cellpadding="3" cellspacing="0" border="0">
     <tr>
      <td class="main" colspan="2">' . sprintf(sysLanguage::get('TEXT_MEMBER_SINCE'), tep_date_short($membership->getMembershipDate())) . '</td>
     </tr>';

if ($appExtension->isEnabled('inventoryCenters') === true){
	$centerID = $addressBook->getAddressInventoryCenter($membership->getRentalAddressId());
	$CustomerInvCenter = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select inventory_center_id, inventory_center_name from ' . TABLE_PRODUCTS_INVENTORY_CENTERS . ' where inventory_center_id = "' . $centerID . '"');

	$infoTable .= '<tr>
		 <td class="main">Inventory Center:</td>
		 <td class="main">' . $CustomerInvCenter[0]['inventory_center_name'] . '</td>
		</tr>';
}

$infoTable .= '<tr>
	 <td class="main">' . sysLanguage::get('TABLE_HEADING_TITLE_REQUESTING') . ':</td>
	 <td class="main">' . $rentalQueue->count_contents() . '</td>
	</tr>
	<tr>
	 <td class="main">' . sysLanguage::get('TABLE_HEADING_PACKAGE_ALLOWED') . ':</td>
	 <td class="main">' . $totalCanSend . '</td>
	</tr>
	<tr>
	 <td class="main">' . sysLanguage::get('TABLE_HEADING_ITEMS') . ':</td>
	 <td class="main">' . $totalRented . ' ( <a href="' . tep_href_link('return_rentals.php', 'cID=' . $cID) . '">' . sprintf(sysLanguage::get('TEXT_RENTED_QUEUE'), $userAccount->getFullName()) . '</a> )</td>
	</tr>
   </table>';
?>
<?php echo $infoTable; ?>
<form name="rental_queue_details" action="<?php echo itw_app_link('action=sendRentals&cID=' . $_GET['cID']);?>" method="post">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
	<div style="text-align:right;"><?php
		$rentButton = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_RENT'))
			->setType('submit');
		echo $rentButton->draw();
		?></div>
</form>