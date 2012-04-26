<?php
	$Field = Doctrine_Core::getTable('CustomersCustomFields')->find((int)$_GET['field_id']);
	if ($Field){
		$Field->delete();
		
		EventManager::attachActionResponse(array(
			'success'  => true,
			'field_id' => (int)$_GET['field_id']
		), 'json');
	}else{
		EventManager::attachActionResponse(array(
			'success' => false
		), 'json');
	}
?>