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

class TemplateManagerWidgetRentalQueue extends TemplateManagerWidget
{

	public function __construct()
	{
		global $App;
		$this->init('rentalQueue');

		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link(null, 'rentals', 'queue'));
		}
	}

	public function show()
	{
		global $rentalQueue;
		if (ALLOW_RENTALS == 'true'){
			$cart_contents_string = '';
			if ($rentalQueue->count_contents() > 0){
				$cart_contents_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
				$products = $rentalQueue->get_products();
				for($i = 0, $n = sizeof($products); $i < $n; $i++){
					$cart_contents_string .= '<tr><td align="right" valign="top" class="infoBoxContents">';

					if (Session::exists('new_products_id_in_queue') === true && Session::get('new_products_id_in_queue') == $products[$i]['id']){
						$cart_contents_string .= '<span class="newItemInCart">';
					}
					else {
						$cart_contents_string .= '<span class="infoBoxContents">';
					}

					$cart_contents_string .= '</span></td><td valign="top" class="infoBoxContents"><a href="' . itw_app_link('products_id=' . $products[$i]['id'], 'product', 'info') . '">';

					if (Session::exists('new_products_id_in_queue') === true && Session::get('new_products_id_in_queue') == $products[$i]['id']){
						$cart_contents_string .= '<span class="newItemInCart">';
					}
					else {
						$cart_contents_string .= '<span class="infoBoxContents">';
					}

					$cart_contents_string .= $products[$i]['priority'] . '.&nbsp;' . $products[$i]['name'] . '</span></a></td></tr>';

					if (Session::exists('new_products_id_in_queue') === true && Session::get('new_products_id_in_queue') == $products[$i]['id']){
						Session::remove('new_products_id_in_queue');
					}
				}
				$cart_contents_string .= '</table>';
			}
			else {
				$cart_contents_string .= sysLanguage::get('WIDGET_RENTALQUEUE_EMPTY');
			}

			$this->setBoxContent($cart_contents_string);

			return $this->draw();
		}
		return false;
	}
}

?>