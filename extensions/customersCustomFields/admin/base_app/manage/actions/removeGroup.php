<?php
	$Group = Doctrine_Core::getTable('CustomersCustomFieldsGroups')->find((int)$_GET['group_id']);
	if ($Group){
		$Group->delete();
		$success = true;
	}else{
		$success = false;
	}
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>