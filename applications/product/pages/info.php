<?php
$pageContents = '';
if ($Product->isValid() === false || $Product->isActive() === false){
	$notFoundDiv = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all')
		->css('padding', '.5em')
		->html(sysLanguage::get('TEXT_PRODUCT_NOT_FOUND'));

	$continueButton = htmlBase::newElement('button')->usePreset('continue')
		->setHref(itw_app_link(null, 'index', 'default'));

	$buttonBar = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content ui-corner-all pageButtonBar')
		->append($continueButton);

	$pageTitle = sysLanguage::get('TEXT_PRODUCT_NOT_FOUND');
	$pageContents .= $notFoundDiv->draw() . $buttonBar->draw();
}
else {
	$pageTitle = $Product->getName();
	if (sysConfig::get('SHOW_PRODUCT_MODEL_ON_INFO') == 'true' && $Product->hasModel()){
		$pageTitle .= '&nbsp;<span class="smallText">[' . $Product->getModel() . ']</span>';
	}
	$Product->updateViews();

	$showAlsoPurchased = false;
	if (isset($_GET['products_id'])){
		$QOrders = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select p.products_id, p.products_image from orders_products opa, orders_products opb, orders o, products p where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . sysConfig::get('MAX_DISPLAY_ALSO_PURCHASED'));
		$num_products_ordered = sizeof($QOrders);
		if ($num_products_ordered >= sysConfig::get('MIN_DISPLAY_ALSO_PURCHASED')){
			$showAlsoPurchased = true;
		}
	}

	$contents = EventManager::notifyWithReturn('ProductInfoBeforeInfo', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}

	$pageContents .= '<div id="tabs"><ul>';
	$pageContents .= '<li><a href="#tabImage"><span>' . sysLanguage::get('TAB_OVERVIEW') . '</span></a></li>';
	if ($showAlsoPurchased === true){
		$pageContents .= '<li><a href="#tabAlsoPurchased"><span>' . sysLanguage::get('TAB_ALSO_PURCHASED') . '</span></a></li>';
	}

	$contents = EventManager::notifyWithReturn('ProductInfoTabHeader', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}
	$pageContents .= '</ul>';

	$pageContents .= '<div id="tabImage">';
	ob_start();
	include($pageTabsFolder . 'tab_overview.php');
	$pageContents .= ob_get_contents();
	ob_end_clean();
	$pageContents .= '</div>';

	if ($showAlsoPurchased === true){
		$pageContents .= '<div id="tabAlsoPurchased">';
		ob_start();
		include($pageTabsFolder . 'tab_also_purchased.php');
		$pageContents .= ob_get_contents();
		ob_end_clean();
		$pageContents .= '</div>';
	}

	$contents = EventManager::notifyWithReturn('ProductInfoTabBody', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}

	$pageContents .= '</div>';

	$contents = EventManager::notifyWithReturn('ProductInfoAfterInfo', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			$pageContents .= $content;
		}
	}
}

$pageButtons = '';

$content = EventManager::notifyWithReturn('ProductInfoButtonBarAddButton', $Product);
if (!empty($content)){
	foreach($content as $html){
		$pageButtons .= $html;
	}
}

$link = itw_app_link('cPath=' . tep_get_product_path($Product->getID()), 'index', 'default');
$lastPath = $navigation->getPath(1);
if ($lastPath){
	$getVars = array();
	if (is_array($lastPath['get'])){
		foreach($lastPath['get'] as $k => $v){
			if ($k == 'app' || $k == 'appPage'){
				continue;
			}
			$getVars[] = $k . '=' . $v;
		}
	}
	else {
		$getVars[] = $lastPath['get'];
	}

	$link = itw_app_link(implode('&', $getVars), $lastPath['app'], $lastPath['appPage'], $lastPath['mode']);
}

$pageButtons .= htmlBase::newElement('button')
	->addClass('infoBack')
	->usePreset('back')
	->setHref($link)
	->draw();

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
