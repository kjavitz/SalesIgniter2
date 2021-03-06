<?php
/**
 * Sales Igniter E-Commerce System
 * Version: {ses_version}
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) {ses_copyright} I.T. Web Experts
 *
 * This script and its source are not distributable without the written consent of I.T. Web Experts
 */

require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
require('includes/classes/data_populate/export.php');
if ($App->getAppPage() == 'details'){
	$oID = (int)$_GET['oID'];

	$Qorder = Doctrine_Query::create()
		->select('customers_id')
		->from('Orders')
		->where('orders_id = ?', $oID)
		->fetchOne();

	$userAccount = new rentalStoreUser($Qorder['customers_id']);
	$userAccount->loadPlugins();

	$Order = new Order($oID);
}

include_once(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
include_once(sysConfig::getDirFsCatalog() . 'includes/classes/http_client.php');

$orders_statuses = array();
$orders_status_array = array();
$Qstatus = Doctrine_Query::create()
	->select('s.orders_status_id, sd.orders_status_name')
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', (int)Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
foreach($Qstatus as $status){
	$orders_statuses[] = array(
		'id'   => $status['orders_status_id'],
		'text' => $status['OrdersStatusDescription'][0]['orders_status_name']
	);
	$orders_status_array[$status['orders_status_id']] = $status['OrdersStatusDescription'][0]['orders_status_name'];
}

if ($action == 'edit' && isset($_GET['oID'])){
	$Qcheck = Doctrine_Query::create()
		->select('orders_id')
		->from('Orders')
		->where('orders_id = ?', (int)$_GET['oID'])
		->execute();
	$order_exists = true;
	if ($Qcheck === false){
		$order_exists = false;
		$messageStack->addSession('pageStack', sprintf(sysLanguage::get('ERROR_ORDER_DOES_NOT_EXIST'), (int)$_GET['oID']), 'error');
	}
}

if ($App->getPageName() == 'packingslip' || $App->getPageName() == 'invoice'){
	$Qcustomer = Doctrine_Query::create()
		->select('customers_id')
		->from('Orders')
		->where('orders_id = ?', $oID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	//require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
	$Order = new Order((int)$_GET['oID']);
}

switch($App->getPageName()){
	case 'default':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE'));
		break;
	case 'details':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE'));
		break;
	case 'invoice':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE'));
		break;
	case 'packingSlip':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE'));
		break;
}
