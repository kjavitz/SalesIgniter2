<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$Ev = Doctrine_Core::getTable('EventManagerEvents')->findOneByEventsId((int)$_GET['eID']);
	if ($Ev){
		$Ev->delete();
	}

	$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_EVENT_REMOVED'), 'success');

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'eID'))), 'redirect');
?>