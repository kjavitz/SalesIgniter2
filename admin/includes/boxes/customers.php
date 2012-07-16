<?php
/*
	Sales Igniter E-Commerce System
	Version: 1.0
	
	I.T. Web Experts
	http://www.itwebexperts.com
	
	Copyright (c) 2010 I.T. Web Experts
	
	This script and its source are not distributable without the written conscent of I.T. Web Experts
*/

$contents = array(
	'text'     => sysLanguage::get('BOX_HEADING_CUSTOMERS'),
	'link'     => false,
	'children' => array()
);

$contents['children']['customersBox'] = array(
	'link'     => false,
	'text'     => sysLanguage::get('BOX_HEADING_CUSTOMERS'),
	'children' => array(
		array(
			'link' => itw_app_link(null, 'customers', 'default', 'SSL'),
			'text' => 'View Customers'
		)
	)
);

EventManager::notify('BoxCustomersAddLink', &$contents);
if (count($contents['children']) == 0){
	$contents = array();
}
?>