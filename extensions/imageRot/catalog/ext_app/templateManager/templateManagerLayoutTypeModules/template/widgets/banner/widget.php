<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TemplateManagerWidgetBanner extends TemplateManagerWidget {

	public function __construct(){
		global $App;
		$this->init('banner', true, __DIR__);
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$banner_group_id = (isset($boxWidgetProperties->selected_banner_group) ? $boxWidgetProperties->selected_banner_group : 0);
			$bannerExt = $appExtension->getExtension('imageRot');
			$htmlText = $bannerExt->showBannerGroup($banner_group_id, 1);

			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>