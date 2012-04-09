<?php
/*
	Related Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class relatedProducts_admin_categories_new_category extends Extension_relatedProducts {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewCategoryTabHeader',
			'NewCategoryTabBody'
		), null, $this);
	}
	
	public function get_category_tree_list($parent_id = '0', $checked = false, $include_itself = true){
		$langId = Session::get('languages_id');
		
		$catList = '';
		$Qcategories = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc("select c.categories_id, cd.categories_name, c.parent_id from categories c, categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$langId . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
		foreach ($Qcategories as $categories){
			$catList .= '<optgroup label="' . $categories['categories_name'] . '">';
			$Qproducts = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select p.products_id, pd.products_name, p.products_model from products p, products_description pd, products_to_categories p2c where p2c.products_id = p.products_id and p2c.categories_id = "' . $categories['categories_id'] . '" and pd.products_id = p.products_id and pd.language_id = "' . $langId . '"');
			foreach($Qproducts as $products){
				$catList .= '<option value="' . $products['products_id'] . '">(' . $products['products_model'] . ") " . $products['products_name'] . '</option>';
			}
			
			if (tep_childs_in_category_count($categories['categories_id']) > 0){
				$catList .= $this->get_category_tree_list($categories['categories_id'], $checked, false);
			}
			$catList .= '</optgroup>';
		}
		return $catList;
	}
	
	public function NewCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Related Products' . '</span></a></li>';
	}
	
	public function NewCategoryTabBody(&$cInfo){
		$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%');

		$table->addHeaderRow(array(
			'columns' => array(
				array('attr' => array('width' => '40%'), 'text' => 'Products'),
				array('text' => '&nbsp;'),
				array('attr' => array('width' => '40%'), 'text' => 'Related')   ,
				array('attr' => array('width' => '40%'), 'text' => 'Related Global')
			)
		));
		
		$relatedProducts = '';
		//print_r($pInfo);
        if (!empty($cInfo->related_products)){
            $related = explode(',', $cInfo->related_products);
            foreach($related as $pID){
                $relatedProducts .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' . tep_get_products_name($pID) . '</span>' . tep_draw_hidden_field('related_products[]', $pID) . '</div>';
            }
        }

		$QrelatedGlobal = Doctrine_Query::create()			
			->from('ProductsRelatedGlobal ')
			->where('type = "C"')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
		if (!empty($QrelatedGlobal)){  
            $relatedG = explode(',', $QrelatedGlobal[0]['related_global']);
			//if($relatedG == '') 
			//$relatedG = $QrelatedGlobal['related_global'];

            foreach($relatedG as $pID){
                $relatedProductsGlobal .= '<div><a href="#" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' . tep_get_products_name($pID) . '</span>' . tep_draw_hidden_field('related_productsGlobal[]', $pID) . '</div>';
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
					'text' => '<button type="button" id="moveRight"><span>&nbsp;&nbsp;>>&nbsp;&nbsp;</span></button>'.
								'<button type="button" id="moveRight"><span>&nbsp;&nbsp;Global >>&nbsp;&nbsp;</span></button>'
				),
				array(
					'addCls' => 'main',
					'attr' => array(
						'id' => 'related',
						'valign' => 'top'
					), 
					'text' => $relatedProducts
				),
				array(
					'addCls' => 'main',
					'attr' => array(
						'id' => 'relatedGlobal',
						'valign' => 'top'
					), 
					'text' => $relatedProductsGlobal
				)	
			)
		));
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $table->draw() . '</div>';
	}
}
?>