<?php
	$ProductsOptions = Doctrine::getTable('ProductsOptions');
	if (isset($_GET['oID'])){
		$Option = $ProductsOptions->findOneByProductsOptionsId($_GET['oID']);
	}else{
		$Option = $ProductsOptions->create();
	}
	
	$Option->option_type = $_POST['option_type'];
	$Option->use_image = (isset($_POST['use_image']) ? '1' : '0');
	$Option->use_multi_image = (isset($_POST['use_multi_image']) ? '1' : '0');
	$Option->update_product_image = (isset($_POST['update_product_image']) ? '1' : '0');
	
	$Descriptions =& $Option->ProductsOptionsDescription;
	foreach($_POST['option_name'] as $langId => $optionName){
		$Descriptions[$langId]->products_options_name = $optionName;
		$Descriptions[$langId]->language_id = $langId;
		
		if ($langId == Session::get('languages_id')){
			$outputName = $optionName;
		}
	}

	$Option->save();
	
	if (isset($_GET['oID'])){
		EventManager::attachActionResponse(array(
			'success'     => true,
			'option_id'   => $Option->products_options_id,
			'option_name' => $outputName
		), 'json');
	}else{
		$optionId = $Option->products_options_id;
		
		$trashBin = htmlBase::newElement('div')->addClass('trashBin')
		->html('<span class="ui-icon ui-icon-trash"></span>' . sysLanguage::get('TEXT_TRASH_BIN'))
		->attr('option_id', $optionId);

		$sortableList = htmlBase::newElement('sortable_list');

		$iconCss = array(
 			'float'    => 'right',
 			'position' => 'relative',
 			'top'      => '-4px',
 			'right'    => '-4px'
		);
		
 		$deleteIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to delete option')
 		->setHref(itw_app_link('appExt=attributes&action=removeOption&option_id=' . $optionId))
 		->css($iconCss);

 		$editIcon = htmlBase::newElement('icon')->setType('wrench')->setTooltip('Click to edit option')
 		->setHref(itw_app_link('appExt=attributes&action=getOptionWindow&option_id=' . $optionId))
 		->css($iconCss);

		$newOptionWrapper = htmlBase::newElement('div')->css(array(
			'float'   => 'left',
			'width'   => '15em',
			'height'  => '12em',
			'padding' => '.3em',
			'margin'  => '.3em'
		))->attr('option_id', $optionId)
		->addClass('ui-widget ui-widget-content ui-corner-all droppableOption draggableOption')
		->html('<b><span class="optionName" option_id="' . $optionId . '">' . $outputName . '</span></b>' . $deleteIcon->draw() . $editIcon->draw() . '<hr>' . $trashBin->draw() . '<hr />' . $sortableList->draw());
		
		EventManager::attachActionResponse($newOptionWrapper->draw(), 'html');
	}
?>