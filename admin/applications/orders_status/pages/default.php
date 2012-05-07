<?php
$Qstatus = Doctrine_Query::create()
	->select('s.orders_status_id, sd.orders_status_name')
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', (int)Session::get('languages_id'))
	->orderBy('s.orders_status_id');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->allowMultipleRowSelect(true)
	->setMainDataKey('status_id')
	->setQuery($Qstatus);

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
	htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
	htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
));

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_ORDERS_STATUS'))
	)
));

$OrdersStatus = &$tableGrid->getResults();
if ($OrdersStatus){
	$id = 0;
	foreach($OrdersStatus as $sInfo){
		$id = $sInfo['orders_status_id'];
		$name = $sInfo['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'];

		if (sysConfig::get('DEFAULT_ORDERS_STATUS_ID') == $id){
			$nameDisplay = '<b>' . $name . ' (' . sysLanguage::get('TEXT_DEFAULT') . ')</b>';
		}
		else {
			$nameDisplay = $name;
		}

		$Qcheck = Doctrine_Query::create()
			->select('COUNT(*) AS count')
			->from('Orders')
			->where('orders_status = ?', $id)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$remove_status = true;
		$deleteMessage = '';
		if (sysConfig::get('ORDERS_STATUS_PROCESSING_ID') == $id){
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_REMOVE_DEFAULT_ORDER_STATUS');
		}
		elseif (sysConfig::get('ORDERS_STATUS_DELIVERED_ID') == $id) {
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_REMOVE_DEFAULT_ORDER_STATUS');
		}
		elseif (sysConfig::get('ORDERS_STATUS_APPROVED_ID') == $id) {
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_REMOVE_DEFAULT_ORDER_STATUS');
		}
		elseif (sysConfig::get('ORDERS_STATUS_WAITING_ID') == $id) {
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_REMOVE_DEFAULT_ORDER_STATUS');
		}
		elseif (sysConfig::get('ORDERS_STATUS_CANCELLED_ID') == $id) {
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_REMOVE_DEFAULT_ORDER_STATUS');
		}
		elseif (sysConfig::get('DEFAULT_ORDERS_STATUS_ID') == $id) {
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_REMOVE_DEFAULT_ORDER_STATUS');
		}
		elseif ($Qcheck[0]['count'] > 0) {
			$remove_status = false;
			$deleteMessage = sysLanguage::get('ERROR_STATUS_USED_IN_ORDERS');
		}
		else {
			$Qhistory = Doctrine_Query::create()
				->select('COUNT(*) AS count')
				->from('OrdersStatusHistory')
				->where('orders_status_id = ?', $id)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qhistory[0]['count'] > 0){
				$remove_status = false;
				$deleteMessage = sysLanguage::get('ERROR_STATUS_USED_IN_HISTORY');
			}
		}

		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-status_id'     => $id,
				'data-can_delete'     => ($remove_status === true ? 'true' : 'false'),
				'data-delete_message' => $deleteMessage
			),
			'columns' => array(
				array('text' => $nameDisplay)
			)
		));
	}
}
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;">
		<?php echo $tableGrid->draw();?>
	</div>
</div>
