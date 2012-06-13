<?php
$Qzones = Doctrine_Query::create()
	->from('GoogleZones')
	->orderBy('google_zones_name asc');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)
	->setQuery($Qzones);

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->usePreset('new')->addClass('newButton'),
	htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable(),
	htmlBase::newElement('button')->usePreset('delete')->addClass('deleteButton')->disable()
));

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_GOOGLE_ZONE_NAME'))
	)
));

$zones = &$tableGrid->getResults();
if ($zones){
	foreach($zones as $zone){
		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-zone_id' => $zone['google_zones_id']
			),
			'columns' => array(
				array('text' => $zone['google_zones_name'])
			)
		));
	}
}
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(sysConfig::get('GOOGLE_API_BROWSER_KEY'));?>&sensor=false"></script>
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
</div>
