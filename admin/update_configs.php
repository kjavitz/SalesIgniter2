<?php
require('includes/application_top.php');

function addConfiguration($key, $group, $title, $desc, $default, $func) {
	$Qcheck = Doctrine_Query::create()
	->select('configuration_id')
	->from('Configuration')
	->where('configuration_key = ?', $key)
	->execute();
	if ($Qcheck->count() <= 0) {
		$newConfig = new Configuration();
		$newConfig->configuration_key = $key;
		$newConfig->configuration_title = $title;
		$newConfig->configuration_value = $default;
		$newConfig->configuration_description = $desc;
		$newConfig->configuration_group_id = $group;
		$newConfig->sort_order = 11;
		$newConfig->set_function = $func;
		$newConfig->save();
	}
	$Qcheck->free();
}

function add_extra_fields($table, $column, $column_attr = 'VARCHAR(255) NULL'){

	$db=sysConfig::get('DB_DATABASE');
	$link = mysql_connect(sysConfig::get('DB_SERVER'), sysConfig::get('DB_SERVER_USERNAME'), sysConfig::get('DB_SERVER_PASSWORD'));
	if (! $link){
		die(mysql_error());
	}
	mysql_select_db($db , $link) or die("Select Error: ".mysql_error());

	$exists = false;
	$columns = mysql_query("show columns from $table");
	while($c = mysql_fetch_assoc($columns)){
		if($c['Field'] == $column){
			$exists = true;
			break;
		}
	}

	if(!$exists){
		mysql_query("ALTER TABLE `$table` ADD `$column`  $column_attr") or die("An error occured when running \n ALTER TABLE `$table` ADD `$column`  $column_attr \n" . mysql_error());
	}

}



function updateConfiguration($key, $group, $title, $desc, $default, $func) {
	$Qcheck = Doctrine_Query::create()
		->select('configuration_id')
		->from('Configuration')
		->where('configuration_key = ?', $key)
		->execute();
	if ($Qcheck->count() == 1) {
		$newConfig = $Qcheck[0];
		if($title != -1){
			$newConfig->configuration_title = $title;
		}
		if($default != -1){
			$newConfig->configuration_value = $default;
		}
		if($desc != -1){
			$newConfig->configuration_description = $desc;
		}
		if($group != -1){
			$newConfig->configuration_group_id = $group;
		}
		$newConfig->sort_order = 11;
		if($func != -1){
			$newConfig->set_function = $func;
		}
		$newConfig->save();
	}
	$Qcheck->free();
}

addConfiguration('SHOW_MANUFACTURER_ON_PRODUCT_INFO', 1, 'Show manufacturer name on product Info', 'Show manufacturer name on product Info', 'false', "tep_cfg_select_option(array('true', 'false'),");

addConfiguration('ORDERS_STATUS_CANCELLED_ID', 1, 'Order Status cancel ID', 'Order Status cancel ID', '7', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ORDERS_STATUS_WAITING_ID', 1, 'Order Status Waiting for Confirmation ID', 'Order Status Waiting for Confirmation ID', '6', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ORDERS_STATUS_APPROVED_ID', 1, 'Order Status Order Approved ID', 'Order Status order Approved ID', '8', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ORDERS_STATUS_PROCESSING_ID', 1, 'Order Status order Processing ID', 'Order Status order Processing ID', '1', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ORDERS_STATUS_DELIVERED_ID', 1, 'Order Status order Delivered ID', 'Order Status order Delivered ID', '3', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ORDERS_STATUS_ESTIMATE_ID', 1, 'Order Status order Estimate ID', 'Order Status order estimate ID', '9', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ORDERS_STATUS_SHIPPED_ID', 1, 'Order Status Order Shipped ID', 'Order Status order Shipped ID', '10', 'tep_cfg_pull_down_order_status_list(');
addConfiguration('ACCOUNT_FISCAL_CODE_REQUIRED', 5, 'Fiscal Code required', 'Fiscal Code required', 'false', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_VAT_NUMBER_REQUIRED', 5, 'VAT Number required', 'VAT Number required', 'false', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_CITY_BIRTH_REQUIRED', 5, 'City of birth required', 'City of birth required', 'false', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_NEWSLETTER', 5, 'Enable newsletter subscription', 'Enable newsletter subscription', 'false', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ENABLE_HTML_EDITOR', 1, 'Use wysiwyg editor for product description', 'Use wysiwyg editor to edit product description', 'true', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('SHOW_ENLAGE_IMAGE_TEXT', 1, 'Show enlarge image text on product info page', 'Show enlarge image text on product info page', 'true', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('PRODUCT_LISTING_TYPE', 8, 'Use rows or columns for product listing', 'Use rows or columns for product listing', 'row', "tep_cfg_select_option(array('row', 'column'),");
addConfiguration('PRODUCT_LISTING_TOTAL_WIDTH', 8, 'When using columns for product listing content area width to use when calculating image width', 'When using columns for product listing content area width to use when calculating image width', '600', "");
addConfiguration('PRODUCT_LISTING_PRODUCTS_COLUMNS', 8, 'When using columns for product listing number of products to display in a row', 'When using columns for product listing number of products to display in a row', '4', "");
addConfiguration('TOOLTIP_DESCRIPTION_ENABLED', 8, 'Enable product image tooltip description for products listing?', 'Enable product image tooltip description for products listing?', 'true', "tep_cfg_select_option(array('true', 'false'),");
addConfiguration('TOOLTIP_DESCRIPTION_BUTTONS', 8, 'Show buttons in product image tooltip description for products listing?', 'Show buttons in product image tooltip description for products listing?', 'true', "tep_cfg_select_option(array('true', 'false'),");

addConfiguration('RENTAL_DAYS_CUSTOMER_PAST_DUE', 16, 'How many days past due the customer is allowed to rent and receive items', 'How many days past due the customer is allowed to rent and receive items', '3', '');
updateConfiguration('DIR_WS_TEMPLATES_DEFAULT', -1, -1, -1, -1, "tep_cfg_pull_down_template_list(");
addConfiguration('SHOW_COMMENTS_CHECKOUT', 1, 'Show comments on checkout', 'Show comments on checkout page', 'true', "tep_cfg_select_option(array('true', 'false'),");

addConfiguration('ACCOUNT_COMPANY_REQUIRED',5, 'Company required', 'Company required','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_SUBURB_REQUIRED',5, 'Suburb required', 'Suburb required','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_GENDER_REQUIRED',5, 'Gender required', 'Gender required','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_DOB_REQUIRED',5, 'DOB required', 'DOB required','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_STATE_REQUIRED',5, 'State required', 'State required','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_VAT_NUMBER',5, 'VAT Number', 'VAT Number','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_TELEPHONE',5, 'Telephone Number', 'Telephone Number','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_FISCAL_CODE',5, 'Fiscal Code', 'Fiscal Code','false',"tep_cfg_select_option(array('true', 'false'),");
addConfiguration('ACCOUNT_CITY_BIRTH', 5, 'City of birth', 'City of birth', 'false', "tep_cfg_select_option(array('true', 'false'),");

/*
		* This part is for completing with the remaining orders statuses if don't exists. In future updates they should be filled
		* */
function addStatus($status_name) {
	$Qstatus = Doctrine_Query::create()
	->select('s.orders_status_id, sd.orders_status_name')
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', (int) Session::get('languages_id'))
	->andWhere('sd.orders_status_name=?', $status_name)
	->orderBy('s.orders_status_id')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (count($Qstatus) <= 0) {
        $Status = new OrdersStatus();

		$Description = $Status->OrdersStatusDescription;
		foreach (sysLanguage::getLanguages() as $lInfo) {
			$Description[$lInfo['id']]->language_id = $lInfo['id'];
			$Description[$lInfo['id']]->orders_status_name = $status_name;
		}
		$Status->save();
	}
}
$EmailTemplatesVariables = Doctrine_Core::getTable('EmailTemplatesVariables');
$EmailTemplatesVariableCheck = $EmailTemplatesVariables->findOneByEmailTemplatesIdAndEventVariable(17,'adminEditLink');

if($EmailTemplatesVariableCheck == false)
{
	$Variable = new EmailTemplatesVariables();
	$Variable->event_variable = 'adminEditLink';
	$Variable->is_conditional = '0';
	$Variable->email_templates_id = '17';
	$Variable->save();
}
addStatus('Waiting Confirmation');
addStatus('Cancelled');
addStatus('Approved');
addStatus('Estimate');
addStatus('Shipped');


add_extra_fields('modules_shipping_zone_reservation_methods','weight_rates','TEXT NULL');
add_extra_fields('modules_shipping_zone_reservation_methods','min_rental_number'," INT( 1 ) NOT NULL DEFAULT  '0'");
add_extra_fields('modules_shipping_zone_reservation_methods','min_rental_type'," INT( 1 ) NOT NULL DEFAULT  '0'");


//update bannerManger

Doctrine_Query::create()
	->update('TemplatesInfoboxes')
	->set('box_path', '?', 'extensions/imageRot/catalog/infoboxes/banner/')
	->set('ext_name', '?', 'imageRot')
	->where('box_code = ?', 'banner')
	->execute();

$pageName = basename($_SERVER['PHP_SELF']);
$pageContent = substr($pageName, 0, strpos($pageName, '.'));
require(DIR_WS_ADMIN_TEMPLATES . ADMIN_TEMPLATE_NAME . TEMPLATE_MAIN_PAGE);

require(DIR_WS_INCLUDES . 'application_bottom.php');
?>