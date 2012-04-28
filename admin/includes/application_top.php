<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
// start profiling
if (isset($_GET['runProfile'])){
	xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}

error_reporting(E_ALL & ~E_DEPRECATED);

function onShutdown() {
	global $ExceptionManager;
	// This is our shutdown function, in
	// here we can do any last operations
	// before the script is complete.

	if ($ExceptionManager->size() > 0){
		echo '<br /><div style="width:98%;margin-right:auto;margin-left:auto;">' . $ExceptionManager->output() . '</div>';
	}
}

register_shutdown_function('onShutdown');

define('APPLICATION_ENVIRONMENT', 'admin');
define('DATE_TIMESTAMP', 'Y-m-d H:i:s');
define('START_MEMORY_USAGE', memory_get_usage());
/* TO BE MOVED LATER -- BEGIN -- */
define('USER_ADDRESS_BOOK_ENABLED', 'True');
date_default_timezone_set('America/New_York');
/* TO BE MOVED LATER -- END -- */

// Start the clock for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());

require((isset($basePath) ? $basePath : '') . '../includes/classes/ConfigReader/Base.php');
require((isset($basePath) ? $basePath : '') . '../includes/classes/MainConfigReader.php');
require((isset($basePath) ? $basePath : '') . '../includes/classes/ModuleConfigReader.php');
require((isset($basePath) ? $basePath : '') . '../includes/classes/ExtensionConfigReader.php');
require((isset($basePath) ? $basePath : '') . '../includes/classes/system_configuration.php');
require((isset($basePath) ? $basePath : '') . '../includes/classes/SesDateTime.php');

/*
 * Load system path/database settings
 */
sysConfig::init();

/* Use sysConfig from here on */
include(sysConfig::getDirFsCatalog() . 'includes/conversionArrays.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Profiler/Base.php');

// Define the project version
sysConfig::set('PROJECT_VERSION', 'Sales Igniter E-Commerce System 1.0');

// Used in the "Backup Manager" to compress backups
define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

require(sysConfig::getDirFsCatalog() . 'ext/Doctrine.php');
spl_autoload_register(array('Doctrine_Core', 'autoload'));
spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
$manager = Doctrine_Manager::getInstance();
//$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
Doctrine_Core::setModelsDirectory(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');
//Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');

$profiler = new Doctrine_Connection_Profiler();

$connString = 'mysql://' . sysConfig::get('DB_SERVER_USERNAME') . ':' . sysConfig::get('DB_SERVER_PASSWORD') . '@' . sysConfig::get('DB_SERVER') . '/' . sysConfig::get('DB_DATABASE');
$conn = Doctrine_Manager::connection($connString, 'mainConnection');

$conn->setListener($profiler);

/*$cacheConnection = Doctrine_Manager::connection(new PDO('sqlite::memory:'), 'cacheConnection');
	$cacheDriver = new Doctrine_Cache_Db(array(
		'connection' => $conn,
		'tableName'  => 'DoctrineCache'
	));
	$conn->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cacheDriver);
	$conn->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, 3600);*/
$conn->setCharset(sysConfig::get('SYSTEM_CHARACTER_SET'));
$conn->setCollate(sysConfig::get('SYSTEM_CHARACTER_SET_COLLATION'));
$manager->setCurrentConnection('mainConnection');

// Define how do we update currency exchange rates
// Possible values are 'oanda' 'xe' or ''
define('CURRENCY_SERVER_PRIMARY', 'oanda');
define('CURRENCY_SERVER_BACKUP', 'xe');

// set application wide parameters
sysConfig::load();

require(sysConfig::getDirFsCatalog() . 'includes/classes/ttfInfo.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/MultipleInheritance.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Importable/Bindable.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Importable/Installable.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/Importable/SortedDisplay.php');

//require(sysConfig::getDirFsCatalog() . 'includes/classes/html/dom/phpQuery.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/htmlBase.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/exceptionManager.php');
$ExceptionManager = new ExceptionManager;
set_error_handler(array($ExceptionManager, 'addError'));
set_exception_handler(array($ExceptionManager, 'add'));

// define our general functions used application-wide
require(sysConfig::getDirFsAdmin() . 'includes/classes/navigation_history.php');
require(sysConfig::getDirFsAdmin() . 'includes/functions/general.php');
require(sysConfig::getDirFsAdmin() . 'includes/functions/html_output.php');

//Email Template Manager Start
require(sysConfig::getDirFsCatalog() . 'includes/classes/email_events.php');
//Email Template Manager End

//Admin begin
require(sysConfig::getDirFsAdmin() . 'includes/functions/password_funcs.php');
//Admin end

// initialize the logger class
require(sysConfig::getDirFsAdmin() . 'includes/classes/logger.php');

// include shopping cart class
/*
	 * Include all classes that could be included in a session variable --BEGIN--
	 */
require(sysConfig::getDirFsCatalog() . 'includes/classes/user/membership.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/user/address_book.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/user.php');
/*
	 * Include all classes that could be included in a session variable --END--
	 */

require(sysConfig::getDirFsCatalog() . 'includes/classes/eventManager/Manager.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/system_modules_loader.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/ModuleInstaller.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/ModuleBase.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/pdfinfoboxes/PDFInfoBoxAbstract.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/modules.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/orderPaymentModules/modules.php');
require(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/modules.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/ProductBase.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/application.php');
require(sysConfig::getDirFsCatalog() . 'includes/classes/extension.php');

$App = new Application;
$appExtension = new Extension;
$appExtension->preSessionInit();

require(sysConfig::getDirFsCatalog() . 'includes/classes/session.php');
Session::init(); /* Initialize the session */

require(sysConfig::getDirFsCatalog() . 'includes/classes/message_stack.php');
$messageStack = new messageStack;

$appExtension->postSessionInit();

// set the language
require(sysConfig::getDirFsCatalog() . 'includes/classes/system_language.php');
sysLanguage::init();

$appExtension->loadExtensions();

$App->loadApplication((isset($_GET['app']) ? $_GET['app'] : ''), (isset($_GET['appPage']) ? $_GET['appPage'] : ''));
if ($App->isValid() === false) {
	die('No valid application found.');
}
$appExtension->initApplicationPlugins();

//Doctrine_Core::initializeModels(Doctrine_Core::getLoadedModels());

$App->loadLanguageDefines();

// entry/item info classes
require(sysConfig::getDirFsAdmin() . 'includes/classes/object_info.php');

// email classes
require(sysConfig::getDirFsAdmin() . 'includes/classes/mime.php');
require(sysConfig::getDirFsAdmin() . 'includes/classes/email.php');

// check if a default currency is set
if (sysConfig::exists('DEFAULT_CURRENCY', true) === false){
	$messageStack->add('footerStack', sysLanguage::get('ERROR_NO_DEFAULT_CURRENCY_DEFINED'), 'error');
}

if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false)){
	$messageStack->add('footerStack', sysLanguage::get('WARNING_FILE_UPLOADS_DISABLED'), 'warning');
}

// navigation history
if (Session::exists('navigationAdmin') === false){
	Session::set('navigationAdmin', new navigationHistory);
}
$navigation = &Session::getReference('navigationAdmin');

require(sysConfig::getDirFsAdmin() . 'includes/add_ccgvdc_application_top.php'); // ICW CREDIT CLASS Gift Voucher Addittion

require(sysConfig::getDirFsCatalog() . 'includes/classes/system_permissions.php');

if ($App->getAppName() != 'login' && basename($_SERVER['PHP_SELF']) != 'stylesheet.php' && basename($_SERVER['PHP_SELF']) != 'javascript.php'){
	sysPermissions::checkLoggedIn();
	sysPermissions::loadPermissions();

	if ($App->getAppPage() != 'noAccess'){
		$accessPermitted = sysPermissions::adminAccessAllowed(
			$App->getAppName(),
			$App->getAppPage(),
			(isset($_GET['appExt']) ? $_GET['appExt'] : null)
		);
		if ($accessPermitted === false){
			tep_redirect(itw_app_link(null, 'index', 'noAccess'));
		}
	}
		
		if ($App->getAppName() != 'database_manager' && Session::exists('DatabaseError')){
			$messageStack->addSession('pageStack', 'There are database errors that must be fixed before you can use the administration area, They are hilighted red below', 'error');
			tep_redirect(itw_app_link(null, 'database_manager', 'default'));
		}
}

require(sysConfig::getDirFsCatalog() . 'includes/classes/ftp/base.php');

// include currencies class and create an instance
require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
$currencies = new currencies();
?>