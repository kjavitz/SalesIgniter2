<?php
$checkedCats = array();
if ($Product->getId() > 0){
	$QcurCategories = Doctrine_Query::create()
		->select('categories_id')
		->from('ProductsToCategories')
		->where('products_id = ?', $Product->getId())
		->execute();
	if ($QcurCategories->count() > 0){
		foreach($QcurCategories->toArray() as $category){
			$checkedCats[] = $category['categories_id'];
		}
		unset($category);
	}
	$QcurCategories->free();
	unset($QcurCategories);
}
echo tep_get_category_tree_list('0', $checkedCats);
unset($checkedCats);
?>