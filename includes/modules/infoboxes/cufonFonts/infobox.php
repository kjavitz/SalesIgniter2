<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCufonFonts extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('cufonFonts');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			//$this->setBoxContent($htmlText);
			return $this->draw();
	}

	public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();

		$javascript = '';


		$javascript .= '/* Cufon Fonts --BEGIN-- */' . "\n" .
		'	$(document).ready(function (){' . "\n" .
        '   $.getScript("'.sysConfig::getDirWsCatalog().'templates/'.Session::get('tplDir').'/fonts/'.$boxWidgetProperties->applied_font.'.js",function(){ '. "\n" .
		'   Cufon.replace("'.$boxWidgetProperties->applied_elements.'");' . "\n" .
		'   }); ' . "\n" .
		'	});' . "\n" .
		'/* Cufon Fonts --END-- */' . "\n";

		return $javascript;
	}
	
	public function onTemplateExport(&$iInfo, $data){
		$widgetProperties = unserialize($iInfo->widget_properties);
		if (!isset($widgetProperties['image_src'])){
			return;
		}
		$fileContent = '';
		ob_start();
?>
		$widgetProperties['image_src'] = str_replace('<?php echo $data['template_name'];?>', $tplName, $widgetProperties['image_src']);
<?php
		$fileContent = ob_get_contents();
		ob_end_clean();
		
		return $fileContent;
	}
}
?>