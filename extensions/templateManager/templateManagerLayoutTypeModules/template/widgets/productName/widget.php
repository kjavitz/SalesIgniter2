<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TemplateManagerWidgetProductName extends TemplateManagerWidget
{

	public function __construct()
	{
		global $App;
		$this->init('productName', false, __DIR__);
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder)
	{
		global $appExtension;
		$htmlText = '';
		if (isset($_GET['products_id'])){
			$Product = new product((int)$_GET['products_id']);
			$htmlText = $Product->getName();
		}
		$this->setBoxContent('<div class="prodname">' . $htmlText . '</div>');
		return $this->draw();
	}
}

?>