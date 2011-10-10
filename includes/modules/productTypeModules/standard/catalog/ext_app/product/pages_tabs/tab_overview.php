<?php
$productID = $Product->getID();
$productID_string = $_GET['products_id'];
$productName = $Product->getName();
$productImage = $Product->getImage();
$thumbUrl = 'imagick_thumb.php?path=rel&imgSrc=';

$image = $thumbUrl . $productImage;
EventManager::notify('ProductInfoProductsImageShow', &$image, &$Product);
?>
<style>
	.productImageGallery a { border:1px solid transparent;display:inline-block;vertical-align:middle;margin:.2em; }
</style>
<?php
	$productsImage = '<div style="text-align:center;float:left;margin:1em;margin-right:2em;" class="ui-widget ui-widget-content ui-corner-all">' .
	'<div style="margin:.5em;text-align:center;"><a id="productsImage" class="fancyBox" href="<?php echo $image;?>">' .
	'<img class="jqzoom" src="' . $image . '&width=250&height=250" alt="' . $image . '" /><br />' .
	sysLanguage::get('TEXT_CLICK_TO_ENLARGE') .
	'</a>' .
	rating_bar($productName,$productID) .
	'</div>';

$AdditionalImages = $Product->getAdditionalImages();
if (sizeof($AdditionalImages) > 0){
	$productsImage .= '<div style="margin:.5em;" class="productImageGallery">' .
		'<div class="ui-widget ui-widget-content ui-corner-all" style="overflow:none;">' .
		'<a class="fancyBox ui-state-active" index="0" rel="gallery" href="' . $image . '"><img class="additionalImage" imgSrc="' . $image . '&width=250&height=250" src="' . $image . '&width=50&height=50"></a>';

	$imgSrc =  'images/';
	$ind = 0;
	foreach($AdditionalImages as $imgInfo){
		$addImage = $thumbUrl . $imgSrc . $imgInfo['file_name'];
		$productImageSrc = $addImage . '&width=250&height=250';
		$thumbSrc = $addImage . '&width=50&height=50';
		$ind++;

		$productsImage .= '<a class="fancyBox" index="'.$ind.'" rel="gallery" href="' . $addImage . '"><img class="additionalImage" imgSrc="' . $productImageSrc . '" src="' . $thumbSrc . '"></a>';
	}

	$productsImage .= '</div>' .
		'</div>' .
		'<div class="main" style="margin:.5em;">Click Image To Select</div>';
}else{
	$productsImage .= '<a class="fancyBox ui-state-active" style="display:none" index="0" rel="gallery" href="' . $image . '"><img class="additionalImage" imgSrc="' . $image . '&width=250&height=250" src="' . $image . '&width=50&height=50"></a>';
}
$productsImage .= '</div>';

echo $productsImage;

echo '<p>';

$contents = EventManager::notifyWithReturn('ProductInfoBeforeDescription', &$Product);
if (!empty($contents)){
	foreach($contents as $content){
		echo $content;
	}
}
if(sysConfig::get('SHOW_MANUFACTURER_ON_PRODUCT_INFO') == 'true'){
	echo sysLanguage::get('TEXT_MANUFACTURER'). $Product->getManufacturerName().'<br />';
}

echo $Product->getDescription() . '<br />';

$contents = EventManager::notifyWithReturn('ProductInfoAfterDescription', &$Product);
if (!empty($contents)){
	foreach($contents as $content){
		echo $content;
	}
}

echo '</p>' .
	'<div style="clear:both;"></div>';

$purchaseBoxes = array();
$purchaseTypes = array();
$ProductType = $Product->getProductTypeClass();
foreach($ProductType->getPurchaseTypes() as $PurchaseType){
	$typeName = $PurchaseType->getCode();
	$purchaseTypes[$typeName] = $PurchaseType;
	if ($purchaseTypes[$typeName]){
		$purchaseTypes[$typeName]->loadProduct($Product->getID());
		$settings = $purchaseTypes[$typeName]->getPurchaseHtml('product_info');
		if (is_null($settings) === false){
			EventManager::notify('ProductInfoPurchaseBoxOnLoad', &$settings, $typeName, $purchaseTypes);
			$purchaseBoxes[] = $settings;
		}
	}
}

$extDiscounts = $appExtension->getExtension('quantityDiscount');
$extAttributes = $appExtension->getExtension('attributes');

$purchaseTable = htmlBase::newElement('table')
	->addClass('ui-widget')
	->css('width', '100%')
	->setCellPadding(5)
	->setCellSpacing(0);

$columns = array();
foreach($purchaseBoxes as $boxInfo){
	if ($extAttributes !== false){
		$boxInfo['content'] .= $extAttributes->pagePlugin->drawAttributes(array(
				'productClass' => $Product,
				'purchase_type' => $boxInfo['purchase_type']
			));
	}

	if ($extDiscounts !== false && $purchaseTypes[$boxInfo['purchase_type']]->hasInventory()){
		$boxInfo['content'] .= $extDiscounts->showQuantityTable(array(
				'productClass' => $Product,
				'purchase_type' => $boxInfo['purchase_type'],
				'product_id' => $Product->getId()
			));
	}

	$boxInfo['content'] .= htmlBase::newElement('input')->attr('type', 'hidden')->setName('products_id')->val($productID)->draw();
	$boxInfo['content'] .= htmlBase::newElement('input')->attr('type', 'hidden')->setName('purchase_type')->val($boxInfo['purchase_type'])->draw();

	$boxObj = htmlBase::newElement('infobox')
		->setForm(array(
			'name' => 'cart_quantity',
			'action' => $boxInfo['form_action']
		))
		->css('width', 'auto')->removeCss('margin-left')->removeCss('margin-right')
		->setHeader($boxInfo['header'])
		->setButtonBarLocation('bottom')
		->addContentRow($boxInfo['content']);

	if ($boxInfo['allowQty'] === true){
		$qtyInput = htmlBase::newElement('input')
			->css('margin-right', '1em')
			->setSize(3)
			->setName('quantity')
			->setLabel('Quantity:')
			->setValue(1)
			->setLabelPosition('before');

		$boxObj->addButton($qtyInput);
	}

	$boxObj->addButton($boxInfo['button']);

	$columns[] = array(
		'align' => 'center',
		'valign' => 'top',
		'text' => $boxObj->draw()
	);

	if (sizeof($columns) > 1){
		$purchaseTable->addBodyRow(array(
				'columns' => $columns
			));
		$columns = array();
	}
}

if (sizeof($columns) > 0){
	$columns[0]['colspan'] = 2;
	$purchaseTable->addBodyRow(array(
			'columns' => $columns
		));
}


echo '<div style="text-align:center;">' .
	$purchaseTable->draw() .
	'<div style="clear:both;"></div>' .
	'</div>' .
	'<div style="clear:both;"></div>';

if ($Product->isBox()){
	$discs = $Product->getDiscs(false, true);
	$totalDiscs = $Product->getTotalDiscs();
	$pageContents .= '<br><div><h4>This product is part of a set (click product name to view details):</h4></div>';
	$hasRentals = false;
}elseif ($Product->isInBox()){
	$discNumber = $Product->getDiscNumber($Product->getID());
	$totalDiscs = $Product->getTotalDiscs();

	echo '<br><div class="main"' . $style . '>' . sprintf(
		sysLanguage::get('TEXT_BS_SERIES'),
		$discNumber,
		$totalDiscs,
		'<a href="' . itw_app_link('products_id='.$Product->getBoxID(), 'product', 'info') . '">' .$Product->getBoxName() . '</a>'
	) .
		'</div>';

	$discs = $Product->getDiscs($Product->getID(), true);
	if (sizeof($discs) > 0){
		echo '<br><div><h4>' .sysLanguage::get('TEXT_BS_OTHER_DISCS') . '</h4></div>';
	}
}

if (isset($discs) && sizeof($discs) > 0){
	$productListing = new productListing_row();
	$productListing->disablePaging()
		->disableSorting()
		->dontShowWhenEmpty()
		->setData($discs);

	echo $productListing->draw();

	if ($Product->isBox() && sizeof($discs) > 1){
		$queueForm = htmlBase::newElement('form')
			->attr('name', 'cart_quantity')
			->attr('action', itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'))
			->attr('method', 'post');

		$prodId = htmlBase::newElement('input')
			->setType('hidden')
			->setName('products_id')
			->setValue($productID);

		$queueAll = htmlBase::newElement('button')
			->setText(sysLanguage::get('TEXT_BUTTON_IN_QUEUE_SERIES'))
			->setType('submit')
			->setName('add_queue_all');

		$queueForm->append($prodId)
			->append($queueAll);

		echo '<div style="text-align:right;margin-top:.3em;">' .
			$queueForm->draw() .
			'</div>';
	}
}

echo '<div style="text-align:center;">';

if ($Product->hasURL()) {
	echo '<div>' .
		sprintf(
			sysLanguage::get('TEXT_MORE_INFORMATION'),
			itw_app_link('action=url&goto=' . urlencode($Product->getURL()), 'redirect', 'default', 'NONSSL')
		) .
		'</div>';
}

if ($Product->isAvailable() === false) {
	echo '<div>' .
		sprintf(
			sysLanguage::get('TEXT_DATE_AVAILABLE'),
			$Product->getDateAvailable()->format(DATE_RSS)
		) .
		'</div>';
} else {
	/*
	   echo '<div>' .
		   sprintf(
			   sysLanguage::get('TEXT_DATE_ADDED'),
			   tep_date_long($Product->getDateAdded())
		   ) .
	   '</div>';
	   */
}

echo '</div>';
?>