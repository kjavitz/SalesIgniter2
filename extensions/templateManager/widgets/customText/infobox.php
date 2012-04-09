<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCustomText extends InfoBoxAbstract
{

	public function __construct() {
		global $App;
		$this->init('customText');
	}

	public function showLayoutPreview($WidgetSettings) {
		global $appExtension;
		$return = '';
		$infoPageExt = $appExtension->getExtension('infoPages');
		if ($infoPageExt && isset($WidgetSettings->selected_page) && !empty($WidgetSettings->selected_page)){
			$return = $infoPageExt->displayContentBlock($WidgetSettings->selected_page);
		}
		elseif (isset($WidgetSettings->custom_text) && !empty($WidgetSettings->custom_text)) {
			$return = $WidgetSettings->custom_text;
		}
		else {
			$return = $this->getBoxCode();
		}
		return $return;
	}

	public function show() {
		global $appExtension;
		$boxWidgetProperties = $this->getWidgetProperties();
		$htmlText = '';
		$infoPageExt = $appExtension->getExtension('infoPages');
		if ($infoPageExt && isset($boxWidgetProperties->selected_page) && !empty($boxWidgetProperties->selected_page)){
			$htmlText = $infoPageExt->displayContentBlock($boxWidgetProperties->selected_page);
		}
		elseif (isset($boxWidgetProperties->custom_text) && !empty($boxWidgetProperties->custom_text)) {
			$htmlText = $boxWidgetProperties->custom_text;
		}
		$this->setBoxContent($htmlText);
		if ($this->getBoxHeading() == ''){
			//$this->setBoxHeading($htmlPage['PagesDescription'][Session::get('languages_id')]['pages_title']);
		}
		return $this->draw();
	}
}

?>