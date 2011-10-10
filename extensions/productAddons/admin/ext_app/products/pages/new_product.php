<?php
class AddonProductsProductClassImport extends MI_Importable
{

	private $_hasAddons = false;

	private $AddonProducts = array();

	public function initAddonProducts() {
		$Qdata = Doctrine_Query::create()
			->select('addon_products')
			->from('Products')
			->where('products_id = ?', $this->getId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qdata && sizeof($Qdata) > 0){
			$Data = $Qdata[0];
			$this->_hasAddons = true;

			$products = explode(',', $Data['addon_products']);
			foreach($products as $pID){
				$this->addAddonProduct($pID);
			}
		}
	}

	public function hasAddonProducts(){
		return $this->_hasAddons;
	}

	public function addAddonProduct($id){
		$this->AddonProducts[] = new Product($id);
	}

	public function getAddonProducts(){
		return $this->AddonProducts;
	}
}

/*
	Related Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class productAddons_admin_products_new_product extends Extension_productAddons {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewProductAddTabs',
			'ProductInfoClassConstruct'
		), null, $this);
	}

	public function ProductInfoClassConstruct(Product &$ProductClass, $Product) {
		$ProductClass->import(new AddonProductsProductClassImport);
		$ProductClass->initAddonProducts();
	}

	public function get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true){
		$langId = Session::get('languages_id');
		
		$catList = '';
		$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$langId . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
		while ($categories = tep_db_fetch_array($categories_query)){
			$catList .= '<optgroup label="' . $categories['categories_name'] . '">';
			$Qproducts = tep_db_query('select p.products_id, pd.products_name, p.products_model from ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . ' pd, ' . TABLE_PRODUCTS_TO_CATEGORIES . ' p2c where p2c.products_id = p.products_id and p2c.categories_id = "' . $categories['categories_id'] . '" and pd.products_id = p.products_id and pd.language_id = "' . $langId . '"');
			while($products = tep_db_fetch_array($Qproducts)){
				$catList .= '<option value="' . $products['products_id'] . '">(' . $products['products_model'] . ") " . $products['products_name'] . '</option>';
			}
			
			if (tep_childs_in_category_count($categories['categories_id']) > 0){
				$catList .= $this->get_category_tree_list($categories['categories_id'], $checked, false);
			}
			$catList .= '</optgroup>';
		}
		return $catList;
	}

	public function NewProductAddTabs(Product $Product, $ProductType, htmlWidget_tabs &$Tabs) {
		if ($ProductType->getCode() == 'standard'){
			$Tabs
				->addTabHeader('tab_' . $this->getExtensionKey(), array('text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS')))
				->addTabPage('tab_' . $this->getExtensionKey(), array('text' => $this->NewProductTabBody($Product)));
		}
	}

	public function NewProductTabBody(Product $Product){
		$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%');

		$table->addHeaderRow(array(
			'columns' => array(
				array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS_TEXT_PRODUCTS')),
				array('text' => '&nbsp;'),
				array('attr' => array('width' => '40%'), 'text' => sysLanguage::get('TAB_PAY_ADDON_PRODUCTS_TEXT_ADDONS'))
			)
		));
		
		$addonProducts = '';
		//print_r($pInfo);
        if ($Product->hasAddonProducts() === true){
            foreach($Product->getAddonProducts() as $AddonProduct){
                $addonProducts .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' . $AddonProduct->getName() . '</span>' . tep_draw_hidden_field('addon_products[]', $AddonProduct->getId()) . '</div>';
            }
        }
		
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'attr' => array(
						'valign' => 'top'
					), 
					'text' => '<select size="30" style="width:100%;" id="productList">' . $this->get_category_tree_list() . '</select>'
				),
				array(
					'addCls' => 'main',
					'text' => '<button type="button" id="moveRightAddon"><span>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span></button>'
				),
				array(
					'addCls' => 'main',
					'attr' => array(
						'id' => 'addons',
						'valign' => 'top'
					), 
					'text' => $addonProducts
				)
			)
		));
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $table->draw() . '</div>';
	}
}
?>