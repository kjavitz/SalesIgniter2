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

class TemplateManagerWidgetCategories extends TemplateManagerWidget
{

	public function __construct()
	{
		global $App;
		$this->init('categories');

		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}

	public function show(TemplateManagerLayoutBuilder $LayoutBuilder)
	{
		global $cPath, $cPath_array, $tree, $categoriesString, $current_category_id;

		$boxWidgetProperties = $this->getWidgetProperties();
		//$showSubcategory = (isset($boxWidgetProperties->showSubcategory)&& $boxWidgetProperties->showSubcategory == 'showSubcategory')?true:false;
		//$showAlways = (isset($boxWidgetProperties->showAlways)&& $boxWidgetProperties->showAlways == 'showAlways')?true:false;
		$excludedCategories = (isset($boxWidgetProperties->excludedCategories) && !empty($boxWidgetProperties->excludedCategories)) ? explode(';', $boxWidgetProperties->excludedCategories) : false;
		$catArrExcl = false;
		if (is_array($excludedCategories) && count($excludedCategories)){
			foreach($excludedCategories as $catExcl){
				$catArr = tep_get_categories('', $catExcl);
				$catArrExcl[] = $catExcl;
				foreach($catArr as $catA){
					$catArrExcl[] = $catA['id'];
				}
			}
			if (is_array($catArrExcl) && count($catArrExcl)){
				$catArrExcl = array_unique($catArrExcl);
			}
		}

		$Qcategories = Doctrine_Query::create()
			->select('c.categories_id, cd.categories_name, c.parent_id')
			->from('Categories c')
			->leftJoin('c.CategoriesDescription cd');

		/*	if($showSubcategory){
			$Qcategories->where('c.parent_id = ?', $current_category_id);
		}else{*/
		$Qcategories->where('c.parent_id = ?', (isset($boxWidgetProperties->selected_category) && (int)$boxWidgetProperties->selected_category > 0) ? $boxWidgetProperties->selected_category : 0);
		//}
		if (is_array($catArrExcl) && count($catArrExcl)){
			$Qcategories->andWhereNotIn('c.categories_id', $catArrExcl);
		}

		$Qcategories->andWhere('(c.categories_menu = "infobox" or c.categories_menu = "both")')
			->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
			->orderBy('c.sort_order, cd.categories_name');

		EventManager::notify('CategoryQueryBeforeExecute', $Qcategories);

		$Result = $Qcategories->execute(array(), Doctrine::HYDRATE_ARRAY);
		if (!isset($boxWidgetProperties->widgetId)){
			$menuId = 'categoriesBoxMenu';
		}
		else {
			$menuId = $boxWidgetProperties->widgetId;
		}

		$menuContainer = htmlBase::newElement('div')
			->attr('id', $menuId)
			->addClass('categoriesBoxMenu');

		if ($Result){
			foreach($Result as $idx => $cInfo){
				$categoryId = $cInfo['categories_id'];
				$parentId = $cInfo['parent_id'];
				$categoryName = $cInfo['CategoriesDescription'][0]['categories_name'];

				$headerEl = htmlBase::newElement('h3');
				if (isset($cPath_array) && $cPath_array[0] == $categoryId){
					$headerEl->addClass('currentCategory');
				}
				$headerEl->html($categoryName);

				$Qchildren = Doctrine_Query::create()
					->select('c.categories_id, cd.categories_name, c.parent_id')
					->from('Categories c')
					->leftJoin('c.CategoriesDescription cd')
					->where('c.parent_id = ?', $categoryId)
					->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
					->orderBy('c.sort_order, cd.categories_name');
				if (is_array($catArrExcl) && count($catArrExcl)){
					$Qchildren->andWhereNotIn('c.categories_id', $catArrExcl);
				}

				EventManager::notify('CategoryQueryBeforeExecute', &$Qchildren);
				$currentChildren = $Qchildren->execute();

				$flyoutContainer = htmlBase::newElement('div');
				$ulElement = htmlBase::newElement('list');
				if ($currentChildren->count() > 0){
					foreach($currentChildren->toArray() as $child){
						addChildren($child, $categoryId, &$ulElement, $catArrExcl);
					}
				}
				else {
					$childLinkEl = htmlBase::newElement('a')
						->addClass('ui-widget ui-widget-content ui-corner-all')
						->css('border-color', 'transparent')
						->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet issingle" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">' . sysLanguage::get('WIDGET_CATEGORIES_VIEW_PRODUCTS') . '</span>')
						->setHref(itw_app_link('cPath=' . $categoryId, 'index', 'default'));

					$liElement = htmlBase::newElement('li')
						->append($childLinkEl)
						->css(array(
						'display' => 'none'
					));
					$ulElement->addItemObj($liElement);
				}
				$flyoutContainer->append($ulElement);

				$menuContainer->append($headerEl)->append($flyoutContainer);
			}
		}

		$this->setBoxContent($menuContainer->draw() . '<div style="text-align:center;font-size:.8em;font-weight:bold;margin:.5em;"><a href="' . itw_app_link(null, 'products', 'all', 'NONSSL') . '">' . sysLanguage::get('WIDGET_CATEGORIES_ALL_PRODUCTS') . '</a></div>');

		return $this->draw();
	}

	public function buildJavascript()
	{
		$boxWidgetProperties = $this->getWidgetProperties();

		ob_start();
		?>
	$('.categoriesBoxMenu').accordion({
	header: 'h3',
	collapsible: true,
	autoHeight: false,
	active:$('.categoriesBoxMenu h3.selected'),
	collapsible: true,
	icons: {
	header: 'ui-icon-circle-triangle-s',
	headerSelected: 'ui-icon-circle-triangle-n'
	}
	});

	$('a', $('.categoriesBoxMenu')).each(function (){
	var $link = $(this);
	$($link.parent()).hover(function (){
	$link.css('cursor', 'pointer').addClass('ui-state-hover');

	var linkOffset = $link.parent().offset();
	var boxOffset = $('.categoriesBoxMenu').offset();
	if ($('ul', $(this)).size() > 0){
	var $menuList = $('ul:first', $(this));
	$menuList.css({
	position: 'absolute',
	top: $link.parent().position().top,
	left: $link.parent().position().left + $link.parent().innerWidth() - 5,
	backgroundColor: '#FFFFFF',
	zIndex: 9999
	}).show();
	}
	}, function (){
	$link.css({cursor: 'default'}).removeClass('ui-state-hover');

	if ($('ul', this).size() > 0){
	$('ul:first', this).hide();
	}
	}).click(function (){
	document.location = $('a:first', this).attr('href');
	});
	});

	$('.categoriesBoxMenu li a.selected').each(function() {
	var li = $(this);
	$('.categoriesBoxMenu').accordion("activate",li.parent().parent().parent().prev());
	});

	$('.categoriesBoxMenu .issingle').each(function() {
	var h3 = $(this).parent().parent().parent().parent().prev();
	var self = $(this);
	h3.click(function (){
	document.location = self.parent().attr('href');
	});

	});
	<?php
		$javascript = ob_get_contents();
		ob_end_clean();

		return $javascript;
	}

	public function buildStylesheet()
	{
		$css = '' . "\n" .
			'.categoriesBoxMenu .ui-accordion-header { ' .
			'color:#ffffff;' .
			'font-weight:bold;' .
			'margin:0;' .
			'padding: .5em;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-header.ui-state-hover { ' .
			'background-color: #f6a864;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-header.ui-state-active { ' .
			'border-color: transparent;' .
			'background-color: #f6a864;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-header .ui-icon { ' .
			'position:relative;' .
			'float:right;' .
			'margin-top:0em;' .
			'margin-right: .5em;' .
			'top: 0;' .
			'background-image: url(/ext/jQuery/themes/icons/ui-icons_ffffff_256x240.png);' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-header.ui-corner-all { ' .
			buildBorderRadius('0px', '0px', '0px', '0px') .
			'border-top: none;' .
			'border-left: none;' .
			'border-right: none;' .
			'border-color: #ffffff;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content { ' .
			'padding: 0;' .
			'margin: 0;' .
			'border:none;' .
			'background: transparent;' .
			buildBorderRadius('0px', '0px', '0px', '0px') .
			'overflow:visible;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content ul { ' .
			'list-style: none;' .
			'padding: 0;' .
			'margin: .1em;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content li { ' .
			'font-size: 1em;' .
			'padding: .1em 0;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content li ul { ' .
			'width: 150px;' .
			'padding: .2em;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content li a { ' .
			'text-decoration: none;' .
			'display:block;' .
			'padding: .1em;' .
			'margin-left: auto;' .
			'margin-right: auto;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content li .ui-icon { ' .
			'margin-right: .3em;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content li a:hover, ' .
			'.categoriesBoxMenu .ui-accordion-content li a.selected { ' .
			'background: #e6e6e6;' .
			' }' . "\n" .
			'.categoriesBoxMenu .ui-accordion-content li .ui-icon { ' .
			'text-indent:0px;' .
			' }' . "\n" .
			'' . "\n";
		ob_start();
		?>
	.categoriesBoxMenu .ui-accordion-content li .ui-icon-triangle-1-e{
	display:inline-block;
	}
	<?php
		$cssSource = ob_get_contents();
		ob_end_clean();
		$css .= $cssSource;

		return $css;
	}
}

?>