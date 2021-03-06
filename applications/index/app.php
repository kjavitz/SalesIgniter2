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

$category_depth = 'top';
$navigation->clear_snapshot();
if (isset($cPath) && tep_not_null($cPath)){
	$navigation->set_snapshot();
	$ProductCount = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select count(*) as total from products_to_categories where categories_id = '" . (int)$current_category_id . "'");

	if ($ProductCount[0]['total'] > 0){
		$category_depth = 'products'; // display products
	}
	else {
		$CategoryCount = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select count(*) as total from categories where parent_id = '" . (int)$current_category_id . "'");

		if ($CategoryCount[0]['total'] > 0){
			$category_depth = 'nested'; // navigate through the categories
		}
		else {
			$category_depth = 'products'; // category has no products, but display the 'no products' message
		}
	}
}

if ($category_depth == 'nested'){
	$QCategory = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc("select cd.categories_name, c.categories_image from categories c, categories_description cd where c.categories_id = '" . (int)$current_category_id . "' and cd.categories_id = '" . (int)$current_category_id . "' and cd.language_id = '" . (int)Session::get('languages_id') . "'");
	$category = $Qcategory[0];

	$App->setAppPage('nested');
}
elseif ($category_depth == 'products') {
	if (sysConfig::get('TOOLTIP_DESCRIPTION_ENABLED') == 'true'){
		$App->addStylesheetFile('ext/jQuery/external/mopTip/mopTip-2.2.css');
		$App->addJavascriptFile('ext/jQuery/external/mopTip/mopTip-2.2.js');
		$App->addJavascriptFile('applications/products/javascript/common.js');
	}
	$App->setAppPage('products');
}
else { // default page
	$App->setAppPage('default');
}
