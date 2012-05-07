<?php
$Qcustomers = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.RentedQueue rq')
	->leftJoin('c.CustomersMembership cm')
	->leftJoin('c.MembershipBillingReport mu on (mu.customers_id = c.customers_id and mu.date = "' . LAST_CRON_DATE . '")')
	->leftJoin('c.CustomersInfo i')
	->leftJoin('c.AddressBook a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)')
	->leftJoin('a.Countries co')
	->orderBy('i.customers_info_date_account_created desc, c.customers_lastname, c.customers_firstname');

/*if (isset($_GET['search']) && !empty($_GET['search'])) {
	$Qcustomers->where('c.customers_lastname like ?', '%' . $_GET['search'] . '%')
		->orWhere('c.customers_firstname like ?', '%' . $_GET['search'] . '%')
		->orWhere('c.customers_email_address like ?', '%' . $_GET['search'] . '%');
} */

if ($_GET['filter'] == '1' || !isset($_GET['filter'])){
	$Qcustomers->andWhere('cm.ismember = "M" and cm.activate = "Y" and DATEDIFF(cm.next_bill_date, CURDATE()) < ' . sysConfig::get('RENTAL_DAYS_CUSTOMER_PAST_DUE') . '');
}
else {
	$Qcustomers->andWhere('cm.ismember = "M" and count(rq.customers_id)>0 and ((cm.activate = "Y" and DATEDIFF(cm.next_bill_date, CURDATE()) < ' . sysConfig::get('RENTAL_DAYS_CUSTOMER_PAST_DUE') . ') OR cm.activate = "N")');
}

EventManager::notify('CustomersListingQueryBeforeExecute', &$Qcustomers);

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit'] : 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 0))
	->setQuery($Qcustomers);

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
	htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
	htmlBase::newElement('button')->setText('Orders')->addClass('ordersButton')->disable(),
	htmlBase::newElement('button')->setText('Email')->addClass('emailButton')->disable()
));

$tableGridHeader = array(
	array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMERS_ID')),
	array('text' => sysLanguage::get('TABLE_HEADING_LASTNAME')),
	array('text' => sysLanguage::get('TABLE_HEADING_FIRSTNAME')),
	array('text' => sysLanguage::get('TABLE_HEADING_MEMBER_OR_USER')),
	array('text' => sysLanguage::get('TABLE_HEADING_MEMBERSHIP_STATUS')),
	array('text' => sysLanguage::get('TABLE_HEADING_ACCOUNT_CREATED'))
);

EventManager::notify('AdminCustomerListingAddHeader', &$tableGridHeader);

$tableGridHeader[] = array(
	'text' => /*sysLanguage::get('TABLE_HEADING_INFO')*/
	'Info'
);
$tableGrid->addHeaderRow(array(
	'columns' => $tableGridHeader
));

$customers = &$tableGrid->getResults();
if ($customers){
	foreach($customers as $customer){
		$customerId = $customer['customers_id'];

		if ((!isset($_GET['cID']) || $_GET['cID'] == $customerId) && !isset($cInfo)){
			$cInfo = new objectInfo($customer);

			$Qreviews = Doctrine_Query::create()
				->select('count(*) as number_of_reviews')
				->from('Reviews')
				->where('customers_id = ?', (int)$customerId)
				->execute()->toArray();
			if (!$Qreviews){
				$cInfo->number_of_reviews = 0;
			}
			else {
				$cInfo->number_of_reviews = (int)$Qreviews[0]['number_of_reviews'];
			}
		}

		$Qorders = Doctrine_Query::create()
			->select('count(*) as total')
			->from('Orders o')
			->where('customers_id = ?', $customerId);

		EventManager::notify('OrdersListingBeforeExecute', &$Qorders);

		$Qorders = $Qorders->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$arrowIcon = htmlBase::newElement('icon')->setType('info');

		if (empty($customer['MembershipBillingReport'])){
		}
		elseif ($customer['MembershipBillingReport']['status'] == 'A') {
			$addCls .= ' dataTableRowA';
		}
		elseif ($customer['MembershipBillingReport']['status'] == 'D') {
			$addCls .= ' dataTableRowD';
		}

		if (!isset($customer['CustomersMembership']) || $customer['CustomersMembership']['ismember'] == 'U'){
			$member = 'User';
		}
		elseif ($customer['CustomersMembership']['ismember'] == 'M') {
			$member = 'Member';
		}
		else {
			$member = 'Unknown';
		}

		if (!isset($customer['CustomersMembership'])){
			$activate = '';
		}
		elseif ($customer['CustomersMembership']['activate'] == 'Y') {
			$activate = 'Active';
		}
		elseif ($customer['CustomersMembership']['activate'] == 'N') {
			$activate = 'InActive';
		}

		if (Session::exists('customer_login_allowed') && Session::get('customer_login_allowed') === true){
			$loginAsCustomerLink = '<a href="' . itw_app_link('action=loginAs&cID=' . $customerId) . '"><b>' . sysLanguage::get('LOGIN_AS_CUSTOMER') . '</b></a>';
		}

		$tableGridBody = array(
			array('text' => $customer['customers_id']),
			array('text' => $customer['customers_lastname']),
			array('text' => $customer['customers_firstname']),
			array(
				'text'  => $member,
				'align' => 'center'
			),
			array(
				'text'  => $activate,
				'align' => 'center'
			),
			array(
				'text'  => tep_date_short($customer['CustomersInfo']['customers_info_date_account_created']),
				'align' => 'center'
			)
		);

		EventManager::notify('AdminCustomerListingAddBody', $customer, &$tableGridBody);

		$tableGridBody[] = array(
			'text'  => $arrowIcon->draw(),
			'align' => 'center'
		);

		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-customer_id'    => $customerId,
				'data-customer_email' => $customer['customers_email_address'],
				'data-has_orders'     => ($Qorders[0]['total'] > 0 ? 'true' : 'false')
			),
			'columns' => $tableGridBody
		));

		$tableGrid->addBodyRow(array(
			'addCls'  => 'gridInfoRow',
			'columns' => array(
				array(
					'colspan' => 6,
					'text'    => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_DATE_ACCOUNT_CREATED') . '</b></td>' .
						'<td> ' . tep_date_short($cInfo->CustomersInfo['customers_info_date_account_created']) . '</td>' .
						'<td><b>' . sysLanguage::get('TEXT_DATE_ACCOUNT_LAST_MODIFIED') . '</b></td>' .
						'<td>' . tep_date_short($cInfo->CustomersInfo['customers_info_date_account_last_modified']) . '</td>' .
						'<td></td>' .
						'</tr>' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_DATE_LAST_LOGON') . '</b></td>' .
						'<td>' . tep_date_short($cInfo->CustomersInfo['customers_info_date_of_last_logon']) . '</td>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_NUMBER_OF_LOGONS') . '</b></td>' .
						'<td>' . $cInfo->CustomersInfo['customers_info_number_of_logons'] . '</td>' .
						'<td></td>' .
						'</tr>' .
						'<tr>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_COUNTRY') . '</b></td>' .
						'<td>' . $cInfo->AddressBook[0]['Countries']['countries_name'] . '</td>' .
						'<td><b>' . sysLanguage::get('TEXT_INFO_NUMBER_OF_REVIEWS') . '</b></td>' .
						'<td>' . $cInfo->number_of_reviews . '</td>' .
						'<td>' . $loginAsCustomerLink . '</td>' .
						'</tr>' .
						'</table>'
				)
			)
		));
	}
}

$array_limit = array(
	array(
		'id'   => '25',
		'text' => '25'
	),
	array(
		'id'   => '100',
		'text' => '100'
	),
	array(
		'id'   => '250',
		'text' => '250'
	)
);
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />

<table cellspacing="0" cellpadding="0" style="width:99%;margin-right:5px;margin-left:5px;">
	<tr>
		<td>
			<table width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td class="dataTableRowD">&nbsp;&nbsp;&nbsp;</td>
					<td class="smallText"><?php echo sysLanguage::get('TEXT_INFO_RECUR_DENIED');?></td>
				</tr>
				<tr>
					<td class="dataTableRowA">&nbsp;&nbsp;&nbsp;</td>
					<td class="smallText"><?php echo sysLanguage::get('TEXT_INFO_RECUR_SUCCESS');?></td>
				</tr>
			</table>
		</td>
		<td align="right">
			<table cellpadding="3" cellspacing="0">
				<tr>
					<td class="smallText" align="right"><?php
						$searchForm = htmlBase::newElement('form')
							->attr('name', 'search')
							->attr('id', 'search')
							->attr('action', itw_app_link(null, null, null, 'SSL'))
							->attr('method', 'get');

						$pageLimit = htmlBase::newElement('selectbox')
							->setName('limit')
							->setId('limit')
							->setLabel(sysLanguage::get('TEXT_SEARCH_RESULTS'))
							->setLabelPosition('before');
						foreach($array_limit as $limitOption){
							$pageLimit->addOption($limitOption['id'], $limitOption['text']);
						}
						$pageLimit->selectOptionByValue(isset($_GET['limit']) ? $_GET['limit'] : '25');


						$filterpastdue = htmlBase::newElement('selectbox')
							->setName('filter')
							->setId('filter')
							->setLabel(sysLanguage::get('TEXT_SEARCH_RESULTS'))
							->setLabelPosition('before');
						$filterpastdue->addOption('1', 'Past Due');
						$filterpastdue->addOption('2', 'Past Due or Inactive with Rentals Out');
						$filterpastdue->selectOptionByValue(isset($_GET['filter']) ? $_GET['filter'] : '1');

						/*$searchField = htmlBase::newElement('input')
												->setName('search')
												->setLabel(sysLanguage::get('HEADING_TITLE_SEARCH'))
												->setLabelPosition('before');

											if (isset($_GET['search'])){
												$searchField->setValue($_GET['search']);
											} */
						$submitb = htmlBase::newElement('button')
							->setType('submit')
							->setText('Search');
						$searchForm->append($pageLimit);
						$searchForm->append($filterpastdue);
						$searchForm->append($submitb);
						echo $searchForm->draw();
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<div style="width:100%;float:left;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
		<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
	<?php
	if (isset($_GET['search']) && tep_not_null($_GET['search'])){
		?>
		<div style="text-align:right;"><?php
			$resetButton = htmlBase::newElement('button')
				->setText(sysLanguage::get('TEXT_BUTTON_RESET'))
				->setHref(itw_app_link(null, null, 'default'));
			echo $resetButton->draw();
			?></div>
		<?php
	}
	?>
</div>