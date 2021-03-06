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
	'text' => sysLanguage::get('BOX_HEADING_MODULES'),
	'link' => false,
	'children' => array()
);

if (sysPermissions::adminAccessAllowed('extensions', 'default') === true){
	$extensionPages = array();
	$sorted = array();
	foreach($appExtension->getExtensions() as $extCls){
		$sorted[$extCls->getExtensionKey()] = $extCls;
	}
	ksort($sorted);

	$k = 0;
	foreach($sorted as $extName => $classObj){
		if ($extName == 'templateManager' || $extName == 'infoPages' || $extName == 'orderCreator' || $extName == 'customFields' || $extName == 'customersCustomFields'){
			continue;
		}
		$k++;
		$pages  = array();
		if (is_dir($classObj->getExtensionDir() . 'admin/base_app/')){
			$extDir = new DirectoryIterator($classObj->getExtensionDir() . 'admin/base_app/');
			foreach($extDir as $extFileObj){
				if ($extFileObj->isDot() === true || $extFileObj->isDir() === false) {
					continue;
				}
				if (file_exists($extFileObj->getPath() . '/' . $extFileObj->getBaseName() . '/.menu_ignore')) {
					continue;
				}

				if (file_exists($extFileObj->getPath() . '/' . $extFileObj->getBaseName() . '/pages/default.php')){
					if (sysPermissions::adminAccessAllowed($extFileObj->getBaseName(), 'default', $classObj->getExtensionKey()) === true){
						$pages[] = array(
							'link' => itw_app_link('appExt=' . $classObj->getExtensionKey(), $extFileObj->getBaseName(), 'default', 'SSL'),
							'text' => ucwords(str_replace('_', ' ', $extFileObj->getBaseName()))
						);
					}
				}
			}
		}

		if (is_dir(sysConfig::getDirFsCatalog() . 'clientData/extensions/' . $classObj->getExtensionKey() . '/admin/base_app')){
			$extDir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'clientData/extensions/' . $classObj->getExtensionKey() . '/admin/base_app/');
			foreach($extDir as $extFileObj){
				if ($extFileObj->isDot() === true || $extFileObj->isDir() === false) {
					continue;
				}
				if (file_exists($extFileObj->getPath() . '/' . $extFileObj->getBaseName() . '/.menu_ignore')) {
					continue;
				}

				if (file_exists($extFileObj->getPath() . '/' . $extFileObj->getBaseName() . '/pages/default.php')){
					if (sysPermissions::adminAccessAllowed($extFileObj->getBaseName(), 'default', $classObj->getExtensionKey()) === true){
						$pages[] = array(
							'link' => itw_app_link('appExt=' . $classObj->getExtensionKey(), $extFileObj->getBaseName(), 'default', 'SSL'),
							'text' => ucwords(str_replace('_', ' ', $extFileObj->getBaseName()))
						);
					}
				}
			}
		}

		if(count($pages) > 0){
			$contents['children'][] = array(
				'link' => itw_app_link('ext=' . $classObj->getExtensionKey(), 'extensions', 'default', 'SSL'),
				'text' => $classObj->getExtensionName(),
				'children' => $pages
			);
		}
	}
}

if (sysPermissions::adminAccessAllowed('modules') === true){
	$orderModules[] = array(
		'link' => itw_app_link('moduleType=accountsReceivable', 'modules', 'default', 'SSL'),
		'text' => 'Sale Modules'
	);

	$orderModules[] = array(
		'link' => itw_app_link('moduleType=orderPayment', 'modules', 'default', 'SSL'),
		'text' => sysLanguage::get('BOX_MODULES_PAYMENT')
	);

	$orderModules[] = array(
		'link' => itw_app_link('moduleType=orderShipping', 'modules', 'default', 'SSL'),
		'text' => sysLanguage::get('BOX_MODULES_SHIPPING')
	);

	$orderModules[] = array(
		'link' => itw_app_link('moduleType=orderTotal', 'modules', 'default', 'SSL'),
		'text' => sysLanguage::get('BOX_MODULES_ORDER_TOTAL')
	);

	$productModules[] = array(
		'link' => itw_app_link('moduleType=purchaseType', 'modules', 'default', 'SSL'),
		'text' => sysLanguage::get('BOX_MODULES_PURCHASETYPES')
	);

	$productModules[] = array(
		'link' => itw_app_link('moduleType=productType', 'modules', 'default', 'SSL'),
		'text' => sysLanguage::get('BOX_MODULES_PRODUCTTYPES')
	);

	$contents['children'][] = array(
		'link' => false,
		'text' => 'Accounts Receivable',
		'children' => $orderModules
	);

	$contents['children'][] = array(
		'link' => false,
		'text' => 'Product Modules',
		'children' => $productModules
	);

	$contents['children'][] = array(
		'link' => itw_app_link('moduleType=cronjob', 'modules', 'default', 'SSL'),
		'text' => 'Cron Job Modules'
	);
}

if (sysPermissions::adminAccessAllowed('coupons') === true){
	$subChildren = array();
	if (sysPermissions::adminAccessAllowed('coupons', 'default') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'coupons', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_COUPON_ADMIN')
		);
	}

	$contents['children'][] = array(
		'link' => false,
		'text' => sysLanguage::get('BOX_HEADING_GV_ADMIN'),
		'children' => $subChildren
	);
}

EventManager::notify('BoxModulesAddLink', &$contents);
if(count($contents['children']) == 0){
	$contents = array();
}
?>