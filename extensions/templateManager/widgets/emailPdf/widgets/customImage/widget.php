<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TemplateManagerEmailPdfWidgetCustomImage extends TemplateManagerEmailPdfWidget
{

	public function __construct() {
		global $App;
		$this->init('customImage');
	}

	public function showLayoutPreview($WidgetSettings) {
		return '<img src="' . $WidgetSettings['settings']->image_source . '" />';
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder) {
		$boxWidgetProperties = $this->getWidgetProperties();
		$htmlCode = $boxWidgetProperties->image_source;
		$htmlLink = '';
		if (!empty($boxWidgetProperties->image_link)){
			ob_start();
			eval("?>" . '<?php echo ' . $boxWidgetProperties->image_link . ';?>');
			$htmlLink = ob_get_contents();
			ob_end_clean();
		}

		if ($htmlLink == ''){
			$htmlText = '<img src="' . $htmlCode . '"/>';
		}
		else {
			$htmlText = '<a href="' . $htmlLink . '">' . '<img src="' . $htmlCode . '"/></a>';
		}
		$this->setBoxContent($htmlText);
		return $this->draw();
	}
}

?>