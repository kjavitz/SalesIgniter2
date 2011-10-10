<?php
	$appContent = $App->getAppContentFile();

	$category_depth = 'top';
	if (isset($cPath) && tep_not_null($cPath)){
		$categories_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int) $current_category_id . "'");
		$cateqories_products = tep_db_fetch_array($categories_products_query);

		if ($cateqories_products['total'] > 0){
			$category_depth = 'products'; // display products
		}else{
			$category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int) $current_category_id . "'");
			$category_parent = tep_db_fetch_array($category_parent_query);

			if ($category_parent['total'] > 0){
				$category_depth = 'nested'; // navigate through the categories
			}else{
				$category_depth = 'products'; // category has no products, but display the 'no products' message
			}
		}
	}

	if ($category_depth == 'nested'){
		$category_query = tep_db_query("select cd.categories_name, c.categories_image from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int) $current_category_id . "' and cd.categories_id = '" . (int) $current_category_id . "' and cd.language_id = '" . (int) Session::get('languages_id') . "'");
		$category = tep_db_fetch_array($category_query);

		$App->setAppPage('nested');
		//$appContent = sysConfig::getDirFsCatalog() . 'applications/index/pages/nested.php';
	}elseif ($category_depth == 'products' || isset($_GET['manufacturers_id'])){
		$App->setAppPage('products');
		//$appContent = sysConfig::getDirFsCatalog() . 'applications/index/pages/products.php';
	}else{ // default page
		$App->setAppPage('default');
		//$appContent = sysConfig::getDirFsCatalog() . 'applications/index/pages/default.php';
	}
?>