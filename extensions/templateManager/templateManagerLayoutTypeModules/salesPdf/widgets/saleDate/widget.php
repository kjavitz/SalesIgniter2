<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class TemplateManagerWidgetSaleDate extends TemplateManagerWidget
{

	public function __construct() {
		global $App;
		$this->init('saleDate', false, __DIR__);
	}

	public function showLayoutPreview($WidgetSettings)
	{
		$htmlText = '';

		$InvData = new SesDateTime();
		if ($WidgetSettings['settings']->short == 'short'){
			$invDate = $InvData->format(sysLanguage::getDateFormat('short'));
		}
		else {
			$invDate = $InvData->format(sysLanguage::getDateFormat('long'));
		}

		if (!empty($WidgetSettings['settings']->text)){
			switch($WidgetSettings['settings']->type){
				case 'top':
					$htmlText = $WidgetSettings['settings']->text . '<br/>' . $invDate;
					break;
				case 'bottom':
					$htmlText = $invDate . '<br/>' . $WidgetSettings['settings']->text;
					break;
				case 'left':
					$htmlText = $WidgetSettings['settings']->text . $invDate;
					break;
				case 'right':
					$htmlText = $invDate . $WidgetSettings['settings']->text;
					break;
			}
		}else{
			$htmlText = $invDate;
		}
		return $htmlText;
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder) {
		$boxWidgetProperties = $this->getWidgetProperties();
		$htmlText = '';
		$Sale = $LayoutBuilder->getVar('Sale');

		$InvData = $Sale->InfoManager->getInfo('date_added');
		if ($boxWidgetProperties->short == 'short'){
			$invDate = $InvData->format(sysLanguage::getDateFormat('short'));
		}
		else {
			$invDate = $InvData->format(sysLanguage::getDateFormat('long'));
		}

		switch($boxWidgetProperties->type){
			case 'top':
				$htmlText = $boxWidgetProperties->text . '<br/>' . $invDate;
				break;
			case 'bottom':
				$htmlText = $invDate . '<br/>' . $boxWidgetProperties->text;
				break;
			case 'left':
				$htmlText = $boxWidgetProperties->text . $invDate;
				break;
			case 'right':
				$htmlText = $invDate . $boxWidgetProperties->text;
				break;
		}
		$this->setBoxContent($htmlText);
		return $this->draw();
	}
}

?>