<?php
/**
 * Sales Igniter E-Commerce System
 * Version: {ses_version}
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) {ses_copyright} I.T. Web Experts
 *
 * This script and its source are not distributable without the written consent of I.T. Web Experts
 */

class TemplateManagerWidgetWhatsNew extends TemplateManagerWidget
{

	public function __construct()
	{
		global $App;
		$this->init('whatsNew', false, __DIR__);

		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link(null, 'products', 'new'));
		}
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder)
	{
		$Qproduct = Doctrine_Query::create()
			->select('p.products_id')
			->from('Products p')
			->where('p.products_status = ?', '1')
			->orderBy('RAND(), p.products_date_added DESC')
			->limit(MAX_RANDOM_SELECT_NEW);

		/*
				 * @TODO: Make the product queries more centralized so we don't have so many events
				 *        lingering around for product queries
				 */
		EventManager::notify('ProductListingQueryBeforeExecute', &$Qproduct);

		$Result = $Qproduct->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Result){
			$product = new Product($Result[0]['products_id']);
			$whats_new_price = '';
			$newPurchaseType = $product->getPurchaseType('new');
			$usedPurchaseType = $product->getPurchaseType('used');
			if ($newPurchaseType->hasInventory()){
				$whats_new_price = $newPurchaseType->displayPrice();
			}
			elseif ($usedPurchaseType->hasInventory()) {
				$whats_new_price = $usedPurchaseType->displayPrice();
			}

			$boxContent = '<center><a href="' . itw_app_link('products_id=' . $product->getID(), 'product', 'info') . '"><img src="/imagick_thumb.php?width=150&height=150&path=rel&imgSrc=' . $product->getImage() . '"></a><br><a href="' . itw_app_link('products_id=' . $product->getID(), 'product', 'info') . '">' . $product->getName() . '</a><br>' . $whats_new_price . '<br><br><a href="' . itw_app_link(null, 'products', 'new') . '">' . sysLanguage::get('WIDGET_WHATSNEW_ALLPRODS') . '</a></center>';

			$this->setBoxContent($boxContent);

			return $this->draw();
		}
		return false;
	}
}

?>