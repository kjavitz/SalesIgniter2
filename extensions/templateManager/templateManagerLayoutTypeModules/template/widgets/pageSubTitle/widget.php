<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TemplateManagerWidgetPageSubTitle extends TemplateManagerWidget
{

	public function __construct()
	{
		global $App;
		$this->init('pageSubTitle', false, __DIR__);
	}

	public function showLayoutPreview($WidgetSettings)
	{
		return 'This is a DEMO Page Subtitle';
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder)
	{
		global $Template, $pageContent;
		$this->setBoxContent('<h3 class="headingSubTitle">' . $pageContent->getVar('pageSubTitle') . '</h3>');
		return $this->draw();
	}
}

?>