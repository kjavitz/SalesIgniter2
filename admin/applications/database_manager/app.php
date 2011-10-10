<?php
function checkModel($modelName){
	global $manager;
	$dbConn = $manager->getCurrentConnection();
	$tableObj = Doctrine_Core::getTable($modelName);
	$isOk = true;
	$resLink = '';
	if ($dbConn->import->tableExists($tableObj->getTableName())){
		$tableObjRecord = $tableObj->getRecordInstance();
		$DBtableColumns = $dbConn->import->listTableColumns($tableObj->getTableName());
		$tableColumns = array();
		foreach($DBtableColumns as $k => $v){
			$tableColumns[strtolower($k)] = $v;
		}
		$modelColumns = $tableObj->getColumns();
		foreach($modelColumns as $colName => $colSettings){
			if ($colName == 'id') continue;
			if (array_key_exists($colName, $tableColumns) === false){
				$isOk = false;
				$resLink = itw_app_link('rType=ajax&action=fixMissingColumns&Model=' . $modelName, 'database_manager', 'default');
				break;
			}
		}
	}else{
		$isOk = false;
		$resLink = itw_app_link('rType=ajax&action=fixMissingTables&Model=' . $modelName, 'database_manager', 'default');
	}
	return array(
		'isOk' => $isOk,
		'resolution' => $resLink
	);
}

Doctrine_Core::loadAllModels();
$appContent = $App->getAppContentFile();
