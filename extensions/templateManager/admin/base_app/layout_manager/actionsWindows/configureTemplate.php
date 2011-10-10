<?php
$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT') . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->find((int) $_GET['tID']);

$templateName = htmlBase::newElement('input')
	->setName('templateName')
	->attr('id', 'templateName')
	->val(($Template->Configuration['NAME'] ? $Template->Configuration['NAME']->configuration_value : ''));

$stylesheetOptions = htmlBase::newElement('selectbox')
	->setName('stylesheet_compression')
	->selectOptionByValue(($Template->Configuration['STYLESHEET_COMPRESSION'] ? $Template->Configuration['STYLESHEET_COMPRESSION']->configuration_value : 'none'));

$stylesheetCache = htmlBase::newElement('checkbox')
	->setName('stylesheet_cache')
	->val('1')
	->setChecked(($Template->Configuration['STYLESHEET_CACHE'] ? $Template->Configuration['STYLESHEET_CACHE']->configuration_value == 1 : false));

$javascriptOptions = htmlBase::newElement('selectbox')
	->setName('javascript_compression')
	->selectOptionByValue(($Template->Configuration['JAVASCRIPT_COMPRESSION'] ? $Template->Configuration['JAVASCRIPT_COMPRESSION']->configuration_value : 'none'));

$javascriptCache = htmlBase::newElement('checkbox')
	->setName('javascript_cache')
	->val('1')
	->setChecked(($Template->Configuration['JAVASCRIPT_CACHE'] ? $Template->Configuration['JAVASCRIPT_CACHE']->configuration_value == 1 : false));

$stylesheetOptions->addOption('none', 'None');
//$stylesheetOptions->addOption('gzip', 'G-Zip');
$stylesheetOptions->addOption('min', 'Minify');
//$stylesheetOptions->addOption('min_gzip', 'Minify + G-Zip');

$javascriptOptions->addOption('none', 'None');
//$javascriptOptions->addOption('gzip', 'G-Zip');
$javascriptOptions->addOption('min', 'Minify');
//$javascriptOptions->addOption('min_gzip', 'Minify + G-Zip');

$SettingsTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0);

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Template Name:'),
			array('text' => $templateName->draw())
		)
	));

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Stylesheet Compression:'),
			array('text' => $stylesheetOptions->draw())
		)
	));

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Cache Stylesheet:'),
			array('text' => $stylesheetCache->draw())
		)
	));

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Javascript Compression:'),
			array('text' => $javascriptOptions->draw())
		)
	));

$SettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Cache Javascript:'),
			array('text' => $javascriptCache->draw())
		)
	));

$infoBox->addContentRow($SettingsTable->draw());

EventManager::attachActionResponse($infoBox->draw(), 'html');
?>