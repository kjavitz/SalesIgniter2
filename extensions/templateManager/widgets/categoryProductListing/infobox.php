<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCategoryProductListing extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('categoryProductListing');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_CATEGORYPRODUCTLISTING'));
	}

	public function show(){
		global $ShoppingCart, $currencies;
		$WidgetProperties = $this->getWidgetProperties();

		$Qcategories = Doctrine_Core::getTable('Categories')
			->findByParentId((int) $WidgetProperties->category_id);
		$catList = '<ul>';
		foreach($Qcategories as $Category){
			$catList .= '<li>';
			$catList .= $Category->CategoriesDescription[Session::get('languages_id')]->categories_name;
			if ($Category->ProductsToCategories->count() > 0){
				if (
					$Category->ProductsToCategories->count() <= $WidgetProperties->max_products ||
					$WidgetProperties->when_max_products == 'limit_products'
				){
					$catList .= '<ul>';
					$maxProducts = $WidgetProperties->max_products;
					foreach($Category->ProductsToCategories as $ProductToCategory){
						$Product = $ProductToCategory->Products;
						if ($Product->products_status == 1){
							$catList .= '<li>' .
								'<a href="' . itw_app_link('products_id=' . $Product->products_id, 'product', 'info') . '">' .
								$Product->ProductsDescription[Session::get('languages_id')]->products_name .
								'</a>' .
								'</li>';
							$maxProducts--;
							if ($maxProducts == 0){
								break;
							}
						}
					}
					$catList .= '</ul>';
				}else{
					$catList .= '<ul>';
					foreach($Category->Children as $SubCategory){
						$catList .= '<li>' .
							'<a href="' . itw_app_link('cPath=' . $SubCategory->parent_id . '_' . $SubCategory->categories_id, 'index', 'default') . '">' .
							$SubCategory->CategoriesDescription[Session::get('languages_id')]->categories_name .
							'</a>' .
							'</li>';
					}
					$catList .= '</ul>';
				}
			}
			$catList .= '</li>';
		}
		$catList .= '</ul>';

		$this->setBoxContent($catList);

		return $this->draw();
	}
}
?>