<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_VIEWED_PRODUCTS');
	?></div>
<br />
<?php
$Qproducts = Doctrine_Query::create()
	->select('p.products_id, pd.products_name, pd.products_viewed, l.name')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('pd.Languages l')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->orderBy('pd.products_viewed DESC');

$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qproducts);

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_NUMBER')),
		array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS')),
		array('text' => sysLanguage::get('TABLE_HEADING_VIEWED'))
	)
));

$Products = &$tableGrid->getResults();
if ($Products){
	$rowNum = 0;
	foreach($Products as $pInfo){
		foreach($pInfo['ProductsDescription'] as $pdInfo){
			$rowNum++;

			if (strlen($rowNum) < 2){
				$rowNum = '0' . $rowNum;
			}

			$tableGrid->addBodyRow(array(
				'columns' => array(
					array('text' => $rowNum),
					array('text' => $pdInfo['products_name'] . '( ' . $pdInfo['Languages']['name'] . ' )'),
					array('text'  => $pdInfo['products_viewed'],
						  'align' => 'right'
					)
				)
			));
		}
	}
}
?>
<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
	<div style="margin:5px;">
		<?php echo $tableGrid->draw();?>
	</div>
</div>
