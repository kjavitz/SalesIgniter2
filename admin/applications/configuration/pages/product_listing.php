<form name="productListing" action="<?php echo itw_app_link('action=saveProductListing');?>" method="post">
<?php
$listingClasses = array();
$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/classes/product_listing/');
while($dirObj->valid()){
	if ($dirObj->isDot() || $dirObj->isDir()){
		$dirObj->next();
		continue;
	}

	$className = substr($dirObj->getBasename(), 0, -4);
	$listingClasses[] = $className;
	$dirObj->next();
}

$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
while($dirObj->valid()){
	if ($dirObj->isDot() || $dirObj->isFile()){
		$dirObj->next();
		continue;
	}

	if (is_dir($dirObj->getPathname() . '/catalog/classes/product_listing/')){
		$listingDirObj = new DirectoryIterator($dirObj->getPathname() . '/catalog/classes/product_listing/');
		while($listingDirObj->valid()){
			if ($listingDirObj->isDot() || $listingDirObj->isDir()){
				$listingDirObj->next();
				continue;
			}

			$className = substr($listingDirObj->getBasename(), 0, -4);
			if (in_array($className, $listingClasses) === false){
				$listingClasses[] = $className;
			}
			$listingDirObj->next();
		}
	}
	$dirObj->next();
}

$templates = array();
$ignoreTemplates = array('email', 'help', 'help-text', 'Closed', 'new');
$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'templates/');
while($dirObj->valid()){
	if ($dirObj->isDot() || $dirObj->isFile() || in_array($dirObj->getBasename(), $ignoreTemplates)){
		$dirObj->next();
		continue;
	}

	$className = substr($dirObj->getBasename(), 0, -4);
	$templates[] = array(
		'id'   => $dirObj->getBasename(),
		'text' => $dirObj->getBasename()
	);

	if (is_dir($dirObj->getPathname() . '/classes/product_listing/')){
		$listingDirObj = new DirectoryIterator($dirObj->getPathname() . '/classes/product_listing/');
		while($listingDirObj->valid()){
			if ($listingDirObj->isDot() || $listingDirObj->isDir()){
				$listingDirObj->next();
				continue;
			}

			$className = substr($listingDirObj->getBasename(), 0, -4);
			if (in_array($className, $listingClasses) === false){
				$listingClasses[] = $className;
			}
			$listingDirObj->next();
		}
	}

	$dirObj->next();
}

sort($listingClasses);
$columnHolder = htmlBase::newElement('div')->attr('id', 'columnsHolder');

$Qlisting = Doctrine_Query::create()
	->from('ProductsListing p')
	->leftJoin('p.ProductsListingDescription pd')
	->orderBy('products_listing_sort_order')
	->where('p.products_listing_allow_sort = 0')
	->execute();
if ($Qlisting){
	foreach($Qlisting->toArray(true) as $listing){
		$listingId = $listing['products_listing_id'];

		$column = htmlBase::newElement('div')
			->addClass('ui-widget ui-widget-content ui-corner-all')
			->css(array(
			'padding'  => '.5em',
			'margin'   => '.5em',
			'width'    => '240px',
			'float'    => 'left',
			'position' => 'relative'
		));

		$nameInputs = htmlBase::newElement('div')
			->append(htmlBase::newElement('b')->html('<u>Display Name</u>'))
			->append(htmlBase::newElement('br'));
		//echo '<pre>';print_r($listing);
		foreach(sysLanguage::getLanguages() as $lInfo){
			$langId = $lInfo['id'];

			$curName = '';
			if (isset($listing['ProductsListingDescription'][$langId])){
				$curName = $listing['ProductsListingDescription'][$langId]['products_listing_heading_text'];
			}

			$nameInput = htmlBase::newElement('input')
				->setName('products_listing_name[' . $listingId . '][' . $langId . ']')
				->setValue($curName)
				->setLabel($lInfo['showName']() . ': ')
				->setLabelPosition('before');

			$nameInputs->append($nameInput)->append(htmlBase::newElement('br'));
		}

		$headingAlign = htmlBase::newElement('radio')
			->addGroup(array(
			'name'      => 'products_listing_heading_align[' . $listingId . ']',
			'checked'   => $listing['products_listing_heading_align'],
			'data'      => array(
				array(
					'label'         => 'Left',
					'labelPosition' => 'after',
					'value'         => 'left'
				),
				array(
					'label'         => 'Center',
					'labelPosition' => 'after',
					'value'         => 'center'
				),
				array(
					'label'         => 'Right',
					'labelPosition' => 'after',
					'value'         => 'right'
				)
			)
		));

		$headingValign = htmlBase::newElement('radio')
			->addGroup(array(
			'name'      => 'products_listing_heading_valign[' . $listingId . ']',
			'checked'   => $listing['products_listing_heading_valign'],
			'data'      => array(
				array(
					'label'         => 'Top',
					'labelPosition' => 'after',
					'value'         => 'top'
				),
				array(
					'label'         => 'Middle',
					'labelPosition' => 'after',
					'value'         => 'middle'
				),
				array(
					'label'         => 'Bottom',
					'labelPosition' => 'after',
					'value'         => 'bottom'
				)
			)
		));

		$sortOrder = htmlBase::newElement('input')
			->setLabel(htmlBase::newElement('b')->html('<u>Display Order:</u><br />')->draw())
			->setLabelPosition('before')
			->setValue($listing['products_listing_sort_order'])
			->setSize('4')
			->setName('products_listing_sort_order[' . $listingId . ']');

		$status = htmlBase::newElement('radio')
			->addGroup(array(
			'name'      => 'products_listing_status[' . $listingId . ']',
			'checked'   => $listing['products_listing_status'],
			'data'      => array(
				array(
					'label'         => 'Enabled',
					'labelPosition' => 'after',
					'value'         => '1'
				),
				array(
					'label'         => 'Disabled',
					'labelPosition' => 'after',
					'value'         => '0'
				)
			)
		));

		$listingModule = htmlBase::newElement('selectbox')
			->setLabel(htmlBase::newElement('b')->html('<u>Listing Module:</u><br />')->draw())
			->setLabelPosition('before')
			->selectOptionByValue($listing['products_listing_module'])
			->setName('products_listing_module[' . $listingId . ']');

		foreach($listingClasses as $className){
			$listingModule->addOption($className, $className);
		}

		$templateGroup = array();
		foreach($templates as $templatesInfo){
			$templateGroup[] = array(
				'label'         => $templatesInfo['text'],
				'labelPosition' => 'after',
				'value'         => $templatesInfo['id']
			);
		}

		$listingTemplate = htmlBase::newElement('checkbox')
			->addGroup(array(
			'name'      => 'products_listing_template[' . $listingId . '][]',
			'separator' => '<br />',
			'checked'   => explode(',', $listing['products_listing_template']),
			'data'      => $templateGroup
		));

		$column->append($nameInputs)
			->append(htmlBase::newElement('div')->css('padding', '.2em'))
			->append(htmlBase::newElement('b')->html('<u>Heading Align:</u><br />'))
			->append($headingAlign)
			->append(htmlBase::newElement('b')->html('<u>Heading Vertical Align:</u><br />'))
			->append($headingValign)
			->append(htmlBase::newElement('div')->css('padding', '.2em'))
			->append($sortOrder)
			->append(htmlBase::newElement('div')->css('padding', '.2em'))
			->append(htmlBase::newElement('b')->html('<u>Status:</u><br />'))
			->append($status)
			->append(htmlBase::newElement('div')->css('padding', '.2em'))
			->append($listingModule)
			->append(htmlBase::newElement('div')->css('padding', '.2em'))
			->append(htmlBase::newElement('b')->html('<u>Template:</u><br />'))
			->append($listingTemplate);

		$deleteIcon = htmlBase::newElement('span')->addClass('ui-icon ui-icon-circle-close')->css(array(
			'position' => 'absolute',
			'top'      => '.3em',
			'right'    => '.3em'
		));
		$columnHolder->append($column->append($deleteIcon));
	}
}
?>
<div style="text-align:right;padding:.5em;"><?php
	echo htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw();
	echo htmlBase::newElement('button')->usePreset('install')->setText('New Column')->addClass('newColumnButton')
		->draw();
	echo '<br /><small>*Note: No changes are saved until the save button is clicked</small>';
	?></div>
<div style="margin-top:1em;width:100%;">
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:.5em;margin-left:.5em;">
		<div style="width:100%;"><?php echo $columnHolder->draw();?></div>
		<div class="ui-helper-clearfix" style=""></div>
	</div>
</div>
<div style="text-align:right;padding:.5em;"><?php
	echo htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw();
	echo htmlBase::newElement('button')->usePreset('install')->setText('New Column')->addClass('newColumnButton')
		->draw();
	echo '<br /><small>*Note: No changes are saved until the save button is clicked</small>';
	?></div>
</form>
<div id="newColumnBox" style="display:none;"><?php
	$column = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all')
		->css(array(
		'padding'  => '.5em',
		'margin'   => '.5em',
		'width'    => '240px',
		'float'    => 'left',
		'position' => 'relative'
	));

	$nameInputs = htmlBase::newElement('div')
		->append(htmlBase::newElement('b')->html('<u>Display Name</u>'))
		->append(htmlBase::newElement('br'));
	foreach(sysLanguage::getLanguages() as $lInfo){
		$langId = $lInfo['id'];

		$nameInput = htmlBase::newElement('input')
			->setName('products_listing_name[new][RandomNumber][' . $langId . ']')
			->setLabel($lInfo['showName']())
			->setLabelPosition('before');

		$nameInputs->append($nameInput)->append(htmlBase::newElement('br'));
	}

	$headingAlign = htmlBase::newElement('radio')
		->setLabel(htmlBase::newElement('b')->html('<u>Heading Align:</u><br />')->draw())
		->setLabelPosition('before')
		->addGroup(array(
		'name'      => 'products_listing_heading_align[new][RandomNumber]',
		'data'      => array(
			array(
				'label' => 'Left',
				'value' => 'left'
			),
			array(
				'label' => 'Center',
				'value' => 'center'
			),
			array(
				'label' => 'Right',
				'value' => 'right'
			)
		)
	));

	$headingValign = htmlBase::newElement('radio')
		->setLabel(htmlBase::newElement('b')->html('<u>Heading Vertical Align:</u><br />')->draw())
		->setLabelPosition('before')
		->addGroup(array(
		'name'      => 'products_listing_heading_valign[new][RandomNumber]',
		'data'      => array(
			array(
				'label' => 'Top',
				'value' => 'top'
			),
			array(
				'label' => 'Middle',
				'value' => 'middle'
			),
			array(
				'label' => 'Bottom',
				'value' => 'bottom'
			)
		)
	));

	$sortOrder = htmlBase::newElement('input')
		->setLabel(htmlBase::newElement('b')->html('<u>Display Order:</u><br />')->draw())
		->setLabelPosition('before')
		->setSize('4')
		->setName('products_listing_sort_order[new][RandomNumber]');

	$status = htmlBase::newElement('radio')
		->setLabel(htmlBase::newElement('b')->html('<u>Status:</u><br />')->draw())
		->setLabelPosition('before')
		->addGroup(array(
		'name'      => 'products_listing_status[new][RandomNumber]',
		'data'      => array(
			array(
				'label' => 'Enabled',
				'value' => '1'
			),
			array(
				'label' => 'Disabled',
				'value' => '0'
			)
		)
	));


	$listingModule = htmlBase::newElement('selectbox')
		->setLabel(htmlBase::newElement('b')->html('<u>Listing Module:</u><br />')->draw())
		->setLabelPosition('before')
		->setName('products_listing_module[new][RandomNumber]');

	foreach($listingClasses as $className){
		$listingModule->addOption($className, $className);
	}

	$templateGroup = array();
	foreach($templates as $templatesInfo){
		$templateGroup[] = array(
			'label' => $templatesInfo['text'],
			'value' => $templatesInfo['id']
		);
	}

	$listingTemplate = htmlBase::newElement('checkbox')
		->setLabel(htmlBase::newElement('b')->html('<u>Template:</u><br />')->draw())
		->setLabelPosition('before')
		->addGroup(array(
		'name'      => 'products_listing_template[new][RandomNumber][]',
		'separator' => '<br />',
		'data'      => $templateGroup
	));

	$column->append($nameInputs)
		->append(htmlBase::newElement('div')->css('padding', '.2em'))
		->append($headingAlign)
		->append(htmlBase::newElement('div')->css('padding', '.2em'))
		->append($headingValign)
		->append(htmlBase::newElement('div')->css('padding', '.2em'))
		->append($sortOrder)
		->append(htmlBase::newElement('div')->css('padding', '.2em'))
		->append($status)
		->append(htmlBase::newElement('div')->css('padding', '.2em'))
		->append($listingModule)
		->append(htmlBase::newElement('div')->css('padding', '.2em'))
		->append($listingTemplate);

	$deleteIcon = htmlBase::newElement('span')->addClass('ui-icon ui-icon-circle-close')->css(array(
		'position' => 'absolute',
		'top'      => '.3em',
		'right'    => '.3em'
	));

	echo $column->append($deleteIcon)->draw();
	?></div>