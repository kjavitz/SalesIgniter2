<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

abstract class InfoBoxAbstract {
	private $boxId = null;
	private $boxHeadingText = null;
	private $boxHeadingLink = null;
	private $boxContent = null;
	private $installed = false;
	private $boxTemplateDefaultDir = null;
	private $boxTemplateDefault = 'box.tpl';
	private $boxWidgetProperties = '';
	private $boxCurrentTemplateDir = null;
	private $boxTemplateFile = null;
	private $boxTemplateDir = null;
	private $extName = null;
	private $templateVars = array();
	
	public function init($boxCode, $extName = null){
		global $App;
		$this->boxCode = $boxCode;
		$this->boxTemplateDefaultDir = sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates/';
		$this->boxCurrentTemplateDir = sysConfig::getDirFsCatalog() . 'templates/' . (Session::exists('tplDir') ? Session::get('tplDir') : 'fallback') . '/boxes/';

		$ResultSet = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from templates_infoboxes where box_code = "' . $this->boxCode . '"');
		if ($ResultSet && sizeof($ResultSet) > 0){
			$this->installed = true;
			$this->boxData = $ResultSet[0];
			
			//$this->setBoxTemplateFile($this->boxData['template_file']);
		}
		
		if ($this->installed === true || $App->getEnv() == 'admin'){
			if (is_null($extName) === false){
				$this->extName = $extName;
				$langPath = 'extensions/' . $extName . '/catalog/infoboxes/' . $this->boxCode . '/language_defines/global.xml';
				$overwritePath = 'includes/languages/' . Session::get('language') . '/' . str_replace('language_defines/', '', $langPath);
				$DoctPath = 'extensions/' . $extName . '/catalog/infoboxes/' . $this->boxCode . '/Doctrine/base/';
			}else{
				$langPath = 'includes/modules/infoboxes/' . $this->boxCode . '/language_defines/global.xml';
				$overwritePath = 'includes/languages/' . Session::get('language') . '/' . str_replace('language_defines/', '', $langPath);
				$DoctPath = 'includes/modules/infoboxes/' . $this->boxCode . '/Doctrine/base/';
			}
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . $langPath);
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . $overwritePath);
			
			if (is_dir(sysConfig::getDirFsCatalog() . $DoctPath)){
				Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . $DoctPath, Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
			}
		}
	}
	
	public function getExtName(){
		return $this->extName;
	}
	
	public function isInstalled(){
		return $this->installed;
	}
	
	public function getBoxCode(){
		return $this->boxCode;
	}
	
	public function setBoxTemplateFile($val){
		$this->boxTemplateFile = $val;
	}
	
	public function setBoxTemplateDir($val){
		$this->boxTemplateDir = $val;
	}
	
	public function setBoxHeading($val){
		$this->boxHeadingText = $val;
	}
	
	public function setBoxHeadingLink($val){
		$this->boxHeadingLink = $val;
	}

	public function setWidgetProperties($val){
		$this->boxWidgetProperties = $val;
	}

	public function getWidgetProperties(){
		return $this->boxWidgetProperties;
	}
	
	public function setBoxContent($val){
		$this->boxContent = $val;
	}

	public function setBoxId($val){
		$this->boxId = $val;
	}
	
	public function getBoxTemplateFile(){
		return $this->boxTemplateFile;
	}
	
	public function getBoxTemplateDir(){
		return $this->boxTemplateDir;
	}
	
	public function getBoxHeading(){
		return $this->boxHeadingText;
	}
	
	public function getBoxHeadingLink(){
		return $this->boxHeadingLink;
	}
	
	public function getBoxContent(){
		return $this->boxContent;
	}
	
	public function show(){
		return $this->draw();
	}

	public function setTemplateVar($var, $val){
		$this->templateVars[$var] = $val;
	}
	
	public function draw(){
		$WidgetSettings = $this->getWidgetProperties();

		$templateFile = $this->boxTemplateDefault;
		if (isset($WidgetSettings->template_file) && is_null($WidgetSettings->template_file) === false){
			$templateFile = $WidgetSettings->template_file;
		}
		
		$boxTemplate = new Template($templateFile, $this->boxTemplateDefaultDir);

		$this->templateVars['boxHeading'] = $this->boxHeadingText;
		$this->templateVars['boxContent'] = $this->boxContent;
		if (!is_null($this->boxId)){
			$this->templateVars['box_id'] = $this->boxId;
		}

		if (is_null($this->boxHeadingLink) === false){
			$link = htmlBase::newElement('a')
			->setHref($this->boxHeadingLink)
			->attr('alt', 'more')
			->attr('title', 'more')
			->addClass('ui-icon ui-icon-circle-triangle-e');

			$this->templateVars['boxLink'] = $link->draw();
		}
		
		$boxTemplate->setVars($this->templateVars);
		
		return $boxTemplate->parse();
	}
}
?>