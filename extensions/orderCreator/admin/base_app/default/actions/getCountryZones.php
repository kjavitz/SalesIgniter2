<?php
	$countryName = $_GET['country'];
	$aType = $_GET['addressType'];

	$QCountry = Doctrine_Query::create()
	->select('c.countries_id, z.zone_name')
	->from('Countries c')
	->leftJoin('c.Zones z')
	->where('countries_name = ?', $countryName)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if (sizeof($QCountry[0]['Zones']) > 0){
		$html = htmlBase::newElement('selectbox');
		if(isset($_GET['state'])){
			$html->selectOptionByValue($_GET['state']);
		}
		foreach($QCountry[0]['Zones'] as $zInfo){
			$html->addOption($zInfo['zone_name'], $zInfo['zone_name']);
		}
	}else{
		$html = htmlBase::newElement('input');
	}
	
	$html->setName('address[' . $aType . '][entry_state]');
	
	EventManager::attachActionResponse($html->draw(), 'html');
?>