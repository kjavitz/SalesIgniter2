<?php

$selectedYoutube = (isset($WidgetSettings->youtube) ? $WidgetSettings->youtube : '');
$selectedFacebook = (isset($WidgetSettings->facebook) ? $WidgetSettings->facebook : '');
$selectedTwitter = (isset($WidgetSettings->twitter) ? $WidgetSettings->twitter : '');
$selectedLinked = (isset($WidgetSettings->linked) ? $WidgetSettings->linked : '');
$selectedBeforeText = (isset($WidgetSettings->beforeText) ? $WidgetSettings->beforeText : '');
$selectedEmail = (isset($WidgetSettings->email) ? $WidgetSettings->email : '');

$linkFacebook = htmlBase::newElement('input')
	->setName('facebook')
	->setValue($selectedFacebook);

$linkYoutube = htmlBase::newElement('input')
	->setName('youtube')
	->setValue($selectedYoutube);

$linkLinked = htmlBase::newElement('input')
	->setName('linked')
	->setValue($selectedLinked);

$beforeText = htmlBase::newElement('input')
	->setName('beforeText')
	->setValue($selectedBeforeText);

$linkTwitter = htmlBase::newElement('input')
	->setName('twitter')
	->setValue($selectedTwitter);

$linkEmail = htmlBase::newElement('input')
	->setName('email')
	->setValue($selectedEmail);

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Social Networks Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Before Text:'),
		array('text' => $beforeText->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Twitter Link:'),
		array('text' => $linkTwitter->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Facebook Link:'),
		array('text' => $linkFacebook->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Youtube Link:'),
		array('text' => $linkYoutube->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Linkedin Link:'),
		array('text' => $linkLinked->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Email Link:'),
		array('text' => $linkEmail->draw())
	)
));
?>