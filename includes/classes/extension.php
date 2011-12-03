<?php
class ExtensionBase
{

	private $_isInstalled = '';

	public function __construct($extensionKey) {
		$this->extName = $extensionKey;
		$this->dir = sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/';

		$this->info = $this->loadXmlFile($this->dir . 'data/info.xml');
		$this->addColumns = $this->loadXmlFile($this->dir . 'data/base/add_columns.xml');
		$this->config = array();

		$configXml = $this->loadXmlFile($this->dir . 'data/base/configuration.xml');
		if (is_null($configXml) === false){
			$this->parseConfigXmlToArray($configXml->Configuration);
		}

		if ($this->isInstalled() === false){
			$this->enabled = (bool)false;
		}
		else {
			$this->enabled = (bool)(sysConfig::get((string)$this->info->status_key) == 'True' ? true : false);
		}
	}

	public function loadXmlFile($filePath) {
		if (file_exists($filePath)){
			return simplexml_load_file($filePath, 'SimpleXMLElement', LIBXML_NOCDATA);
		}
		return null;
	}

	public function parseConfigXmlToArray($data) {
		foreach((array)$data as $configKey => $configSettings){
			$this->config[] = $configKey;
		}
	}

	public function getOtherExtensionsConfig() {
		global $appExtension;
		foreach($appExtension->extensionDirs as $dir){
			$curPath = $dir['pathname'] . '/data/ext/' . $this->extName . '/';
			if (file_exists($curPath . 'configuration.xml')){
				$configData = $this->loadXmlFile($curPath . 'configuration.xml');
				if (sizeof($configData) > 0){
					$this->parseConfigXmlToArray($configData->Configuration);
				}
			}
		}
	}

	public function isInstalled() {
		if ($this->_isInstalled == ''){
			$this->_isInstalled = sysConfig::exists((string)$this->info->status_key, false);
		}
		return $this->_isInstalled;
	}

	public function isEnabled() {
		return $this->enabled;
	}

	public function getExtensionKey() {
		return (string)$this->info->key;
	}

	public function getExtensionConfigKeys() {
		return (array)$this->config;
	}

	public function getExtensionVersion() {
		return (string)$this->info->version;
	}

	public function getExtensionName() {
		return (string)$this->info->name;
	}

	public function getExtensionDir() {
		return $this->dir;
	}

	public function hasDoctrine() {
		return is_dir($this->dir . 'Doctrine/');
	}

	public function setUpAddColumns() {
		global $manager, $messageStack;
		$dbConn = $manager->getCurrentConnection();
		if ($this->enabled && sizeof($this->addColumns) > 0){
			foreach((array)$this->addColumns as $tableName => $cols){
				if (Doctrine_Core::isValidModelClass($tableName)){
					$tableObj = Doctrine_Core::getTable($tableName);
					$tableObjRecord = $tableObj->getRecordInstance();

					$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

					foreach((array)$cols as $colName => $colSettings){
						$colSettings = (array)$colSettings;
								
						$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
						if (!isset($tableColumns[$colName])){
							ExceptionManager::report('Database table column does not exist.', E_USER_ERROR, array(
									'Extension Name' => $this->extName,
									'Table Name' => $tableName,
									'Column Name' => $colName,
									'Resoultion' => '<a href="' . itw_app_link('action=fixMissingColumns&extName=' . $this->extName, 'extensions', 'default') . '">Click here to resolve</a>'
								));
						}
						else {
							$tableObjRecord->hasColumn($colName, $colSettings['type'], $length, $colSettings);
						}
					}
				}
			}
		}
	}

	public function setUpExtAddColumns() {
		global $manager, $appExtension, $messageStack;
		$dbConn = $manager->getCurrentConnection();
		foreach($appExtension->extensionDirs as $dir){
			if (is_dir($dir['pathname'] . '/data/ext/' . $this->extName) && file_exists($dir['pathname'] . '/data/ext/' . $this->extName . '/add_columns.xml')){
				$extCheck = $appExtension->getExtension($dir['basename']);
				if ($extCheck !== false && $extCheck->isInstalled() === true){
					$addColumns = $this->loadXmlFile($dir['pathname'] . '/data/ext/' . $this->extName . '/add_columns.xml');
					foreach((array)$addColumns as $tableName => $cols){
						if (Doctrine_Core::isValidModelClass($tableName)){
							$tableObj = Doctrine_Core::getTable($tableName);
							$tableObjRecord = $tableObj->getRecordInstance();

							$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

							foreach((array)$cols as $colName => $colSettings){
								$colSettings = (array)$colSettings;
								
								$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
								if (!isset($tableColumns[$colName])){
									ExceptionManager::report('Database table column does not exist.', E_USER_ERROR, array(
											'Extension Name' => $this->extName,
											'Table Name' => $tableName,
											'Column Name' => $colName,
											'Resoultion' => '<a href="' . itw_app_link('action=fixMissingColumns&extName=' . $this->extName, 'extensions', 'default') . '">Click here to resolve</a>'
										));
								}
								else {
									$tableObjRecord->hasColumn($colName, $colSettings['type'], $length, $colSettings);
								}
							}
						}
					}
				}
			}
		}
	}

	public function setUpDoctrine() {
		global $App;
		if ($this->hasDoctrine()){
			$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/');
			foreach($extObj as $eInfo){
				if ($eInfo->isDot() || $eInfo->isDir()) {
					continue;
				}
				Doctrine_Core::addExtModelsDirectory($eInfo->getBasename('.php'), $eInfo->getPath() . '/');
			}
			//Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/', Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

			if (isset($_GET['verifyModels'])){
				$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/');
				foreach($extObj as $eInfo){
					if ($eInfo->isDot() || $eInfo->isDir()) {
						continue;
					}

					$App->checkModel($eInfo->getBasename('.php'), $this->extName);
				}
			}
		}
	}

	public function setUpExtDoctrine() {
		global $App, $appExtension;
		foreach($appExtension->extensionDirs as $dir){
			if (is_dir($dir['pathname'] . '/Doctrine/ext/' . $this->extName)){
				$extCheck = $appExtension->getExtension($dir['basename']);
				if ($extCheck !== false && $extCheck->isInstalled() === true){
					$exteObj = new DirectoryIterator($dir['pathname'] . '/Doctrine/ext/' . $this->extName);
					foreach($exteObj as $eeInfo){
						if ($eeInfo->isDot() || $eeInfo->isDir()) {
							continue;
						}

						Doctrine_Core::addExtModelsDirectory($eeInfo->getBasename('.php'), $eeInfo->getPath() . '/');
					}
					//Doctrine_Core::loadModels($eInfo->getPathname() . '/Doctrine/ext/' . $this->extName, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

					if (isset($_GET['verifyModels'])){
						$extObj = new DirectoryIterator($dir['pathname'] . '/Doctrine/ext/' . $this->extName);
						foreach($extObj as $eeInfo){
							if ($eeInfo->isDot() || $eeInfo->isDir()) {
								continue;
							}

							$App->checkModel($eeInfo->getBasename('.php'), $this->extName);
						}
					}
				}
			}
		}
	}

	public function fixMissingColumns() {
		global $manager, $App, $appExtension, $messageStack;
		$dbConn = $manager->getCurrentConnection();

		if ($this->hasDoctrine()){
			$extObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/' . $this->extName . '/Doctrine/base/');
			foreach($extObj as $eInfo){
				if ($eInfo->isDot() || $eInfo->isDir()) {
					continue;
				}
				$App->addMissingModelColumns($eInfo->getBasename('.php'), $this->extName);
			}
		}

		if ($this->enabled && sizeof($this->addColumns) > 0){
			foreach((array)$this->addColumns as $tableName => $cols){
				if (Doctrine_Core::isValidModelClass($tableName)){
					$tableObj = Doctrine_Core::getTable($tableName);

					$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

					foreach((array)$cols as $colName => $colSettings){
						$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
						if (!isset($tableColumns[$colName])){
							$dbConn->export->alterTable($tableObj->getTableName(), array(
									'add' => array(
										$colName => (array)$colSettings
									)
								));
							$messageStack->addSession('pageStack', '<table><tr><td><b>Server Message:</b></td><td>Database table column added.</td></tr><tr><td><b>Extension Key:</b></td><td>' . $this->extName . '</td></tr><tr><td><b>Table Name:</b></td><td>' . $tableName . '</td></tr><tr><td><b>Column Name:</b></td><td>' . $colName . '</td></tr></table>', 'success');
						}
					}
				}
			}
		}

		foreach($appExtension->extensionDirs as $dir){
			$extCheck = $appExtension->getExtension($dir['basename']);
			if ($extCheck !== false && $extCheck->isInstalled() === true){
				if (is_dir($dir['pathname'] . '/data/ext/' . $this->extName)){
					$addColumns = simplexml_load_file($dir['pathname'] . '/data/ext/' . $this->extName . '/add_columns.xml', 'SimpleXMLElement', LIBXML_NOCDATA);
					foreach((array)$addColumns as $tableName => $cols){
						if (Doctrine_Core::isValidModelClass($tableName)){
							$tableObj = Doctrine_Core::getTable($tableName);
							$tableObjRecord = $tableObj->getRecordInstance();

							$tableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());

							foreach((array)$cols as $colName => $colSettings){
								$length = (isset($colSettings['length']) ? $colSettings['length'] : null);
								if (!isset($tableColumns[$colName])){
									$dbConn->export->alterTable($tableObj->getTableName(), array(
											'add' => array(
												$colName => (array)$colSettings
											)
										));
									$messageStack->addSession('pageStack', '<table><tr><td><b>Server Message:</b></td><td>Database table column added.</td></tr><tr><td><b>Extension Key:</b></td><td>' . $this->extName . '</td></tr><tr><td><b>Table Name:</b></td><td>' . $tableName . '</td></tr><tr><td><b>Column Name:</b></td><td>' . $colName . '</td></tr></table>', 'success');
								}
							}
						}
					}
				}
			}
		}
	}
}

class Extension
{

	public $extensionDirs = array();

	public function __construct() {
		$this->extDir = sysConfig::getDirFsCatalog() . 'extensions/';
		$this->extBasefile = 'ext.php';
		$this->extensions = array();

		$this->extensionDirs = array();
		$dirObj = new DirectoryIterator($this->extDir);
		while($dirObj->valid()){
			if ($dirObj->isDot() || $dirObj->isFile()){
				$dirObj->next();
				continue;
			}

			$this->extensionDirs[] = array(
				'basename' => $dirObj->getBasename(),
				'pathname' => $dirObj->getPathname()
			);

			$dirObj->next();
		}
	}

	private function loadExtensionClasses() {
		foreach($this->extensionDirs as $dir){
			if (!file_exists($dir['pathname'] . '/' . $this->extBasefile)){
				continue;
			}

			$extension = $dir['basename'];

			$className = 'Extension_' . $extension;
			if (!class_exists($className)){
				require($dir['pathname'] . '/' . $this->extBasefile);
			}

			if (!isset($this->extensions[$extension])){
				$this->extensions[$extension] = new $className;
			}

			if ($this->extensions[$extension]->isInstalled() === false){
				unset($this->extensions[$extension]);
			}
		}
	}

	public function preSessionInit() {
		if (sizeof($this->extensions) == 0) {
			$this->loadExtensionClasses();
		}

		foreach($this->extensions as $extension => $extCls){
			if (method_exists($extCls, 'preSessionInit') === true){
				$extCls->preSessionInit();
			}
		}
	}

	public function postSessionInit() {
		if (sizeof($this->extensions) == 0) {
			$this->loadExtensionClasses();
		}

		foreach($this->extensions as $extension => $extCls){
			if (method_exists($extCls, 'postSessionInit') === true){
				$extCls->postSessionInit();
			}
		}
	}

	public function isAdmin() {
		global $App;
		return ($App->getEnv() == 'admin');
	}

	public function isCatalog() {
		global $App;
		return ($App->getEnv() == 'catalog');
	}

	public function loadExtensions() {
		if (sizeof($this->extensions) == 0) {
			$this->loadExtensionClasses();
		}
		/*
		 * Must be separate from the foreach below in order for the ext doctrine and add columns to work
		 */
		foreach($this->extensions as $extension => $extCls){
			$this->extensions[$extension]->setUpDoctrine();
		}

		foreach($this->extensions as $extension => $extCls){
			$extCls->setUpExtDoctrine();
			$extCls->setUpAddColumns();
			$extCls->setUpExtAddColumns();
		}

		$this->initExtensions();
	}

	public function bindMethods(&$class) {
		foreach($this->extensions as $extension => $extCls){
			if (method_exists($extCls, 'bindMethods')){
				$extCls->bindMethods($class);
			}
		}
	}

	public function getLanguageFiles($appInfo, &$filesArray) {
		global $App;
		$findDirBase = $appInfo['env'] . '/base_app/' . $appInfo['appName'] . '/language_defines/';

		$findDirExt = $appInfo['env'] . '/ext_app/';
		if (isset($_GET['appExt'])){
			$findDirExt .= $_GET['appExt'] . '/';
		}
		$findDirExt .= $appInfo['appName'] . '/language_defines/';

		foreach($this->extensionDirs as $dir){
			if ($dir['basename'] == $appInfo['appName']){
				continue;
			}

			if (is_dir($dir['pathname'] . '/' . $findDirBase)){
				$filesArray[] = $dir['pathname'] . '/' . $findDirBase;
			}

			if (is_dir($dir['pathname'] . '/' . $findDirExt)){
				$ext = $this->getExtension($dir['basename']);
				if ($ext !== false && $ext->isEnabled()){
					$filesArray[] = $dir['pathname'] . '/' . $findDirExt;
				}
			}
		}
	}

	public function getOverwriteLanguageFiles($appInfo, &$filesArray) {
		global $App;
		$findDirBase = $appInfo['env'] . '/base_app/' . $appInfo['appName'] . '/';

		$findDirExt = $appInfo['env'] . '/ext_app/';
		if (isset($_GET['appExt'])){
			$findDirExt .= $_GET['appExt'] . '/';
		}
		$findDirExt .= $appInfo['appName'] . '/';

		if (is_dir(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/extensions/')){
			$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/extensions/');
			while($dirObj->valid()){
				if ($dirObj->isDot() || $dirObj->isFile() || $dirObj->getBasename() == $appInfo['appName']){
					$dirObj->next();
					continue;
				}

				if (is_dir($dirObj->getPathname() . '/' . $findDirBase)){
					$filesArray[] = $dirObj->getPathname() . '/' . $findDirBase;
				}

				if (is_dir($dirObj->getPathname() . '/' . $findDirExt)){
					$ext = $this->getExtension($dirObj->getBasename());
					if ($ext !== false && $ext->isEnabled()){
						$filesArray[] = $dirObj->getPathname() . '/' . $findDirExt;
					}
				}
				$dirObj->next();
			}
		}
	}

	public function getGlobalFiles($folder, $appInfo, &$filesArray) {
		global $App;
		$findDir = $appInfo['env'] . '/' . $folder . '/';

		if (isset($appInfo['format']) && $appInfo['format'] == 'relative'){
			$returnDir = sysConfig::getDirWsCatalog() . 'extensions/';
		}
		else {
			$returnDir = sysConfig::getDirFsCatalog() . 'extensions/';
		}

		foreach($this->extensionDirs as $dir){
			if (is_dir($dir['pathname'] . '/' . $findDir)){
				$ext = $this->getExtension($dir['basename']);
				if ($ext !== false && $ext->isEnabled()){
					$globalDirObj = new DirectoryIterator($dir['pathname'] . '/' . $findDir);
					while($globalDirObj->valid()){
						if ($globalDirObj->isDot() || $globalDirObj->isDir()){
							$globalDirObj->next();
							continue;
						}

						$filesArray[] = $returnDir . $dir['basename'] . '/' . $findDir . $globalDirObj->getBasename();

						$globalDirObj->next();
					}
				}
			}
		}
	}

	public function getAppFiles($folder, $appInfo, &$filesArray, $verifyFiles = true) {
		global $App;
		$findDir = $appInfo['env'] . '/ext_app/';

		if (isset($appInfo['appExt']) && $appInfo['appExt'] !== false){
			$findDir .= $appInfo['appExt'] . '/';
		}
		elseif (isset($_GET['appExt'])) {
			$findDir .= $_GET['appExt'] . '/';
		}

		$findDir .= $appInfo['appName'] . '/' . $folder . '/';

		if (isset($appInfo['appFile'])){
			$appFile = $appInfo['appFile'];
		}

		if (isset($appInfo['format']) && $appInfo['format'] == 'relative'){
			$returnDir = sysConfig::getDirWsCatalog() . 'extensions/';
		}
		else {
			$returnDir = sysConfig::getDirFsCatalog() . 'extensions/';
		}

		foreach($this->extensionDirs as $dir){
			if ($dir['basename'] == $appInfo['appName']){
				continue;
			}

			if ($verifyFiles === true){
				if (file_exists($dir['pathname'] . '/' . $findDir . $appFile)){
					$ext = $this->getExtension($dir['basename']);
					if ($ext !== false && $ext->isEnabled()){
						$filesArray[] = $returnDir . $dir['basename'] . '/' . $findDir . $appFile;
					}
				}
			}
			else {
				if (is_dir($dir['pathname'] . '/' . $findDir)){
					$ext = $this->getExtension($dir['basename']);
					if ($ext !== false && $ext->isEnabled()){
						$filesArray[] = $returnDir . $dir['basename'] . '/' . $findDir;
					}
				}
			}
		}
	}

	public function initExtensions() {
		global $App;
		$pagePlugins = array();
		$this->getAppFiles('pages', array(
				'env' => $App->getEnv(),
				'appExt' => (isset($_GET['appExt']) ? $_GET['appExt'] : false),
				'appName' => $App->getAppName(),
				'appFile' => $App->getAppPage() . '.php',
				'format' => 'absolute'
			), &$pagePlugins);

		foreach($pagePlugins as $filePath){
			require($filePath);
		}

		foreach($this->extensions as $extName => $clsObj){
			$clsObj->init();

			if (!empty($pagePlugins)){
				$className = $extName . '_' . $App->getEnv() . '_';
				if (isset($_GET['appExt'])){
					$className .= $_GET['appExt'] . '_';
				}
				$className .= $App->getAppName() . '_' . $App->getAppPage();

				if (class_exists($className) === true){
					$clsObj->pagePlugin = new $className;
					$clsObj->pagePlugin->load();
				}

				unset($className);
			}
		}
	}

	public function isInstalled($extName) {
		return isset($this->extensions[$extName]);
	}

	public function isEnabled($extName) {
		if (($ext = $this->getExtension($extName)) !== false){
			return $ext->isEnabled();
		}
		return false;
	}

	public function getExtension($extName) {
		if (isset($this->extensions[$extName])){
			return $this->extensions[$extName];
		}
		return false;
	}

	public function registerAsResource($name, &$resource) {
		$this->resources[$name] = $resource;
	}

	public function &getResource($name) {
		return $this->resources[$name];
	}

	public function getExtensions() {
		return $this->extensions;
	}
}

?>
