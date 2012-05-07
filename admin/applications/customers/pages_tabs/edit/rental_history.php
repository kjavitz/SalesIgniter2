<?php
$QCustomers = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc("SELECT p.products_id,r.date_added,r.shipment_date,r.return_date, p.products_name, r.broken,r.products_barcode FROM rented_products r, products_description p where p.products_id = r.products_id and p.language_id='" . Session::get('languages_id') . "' and r.return_date > '0000-00-00 00:00:00' and customers_id =" . $cID);
$templateParsed = array();
if (sizeof($QCustomers) < 1){
	$templateParsed[] = '<tr>
       <td colspan="6" class="messageStackError">' . sysLanguage::get('TEXT_INFO_NO_RENTAL_HISTORY') . '</td>
      </tr>';
}
else {
	$template = '<tr class="dataTableRow">
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
       <td class="smallText" align="left">%s</td>
      </tr>';
	foreach($Qcustomers as $customers){
		$templateParsed[] = sprintf($template,
			$customers['products_id'],
			$customers['products_barcode'],
			'<a href="' . tep_href_link(FILENAME_RENT_INVENTORY_COMMENTS, 'pID=' . $customers['products_id']) . '">' . $customers['products_name'] . '</a>',
			date('Y-m-d', strtotime($customers['shipment_date'])),
			date('Y-m-d', strtotime($customers['return_date'])),
			($customers['broken'] ? sysLanguage::get('TEXT_RECEIVED_BROKEN') : sysLanguage::get('TEXT_RECEIVED_OK'))
		);
	}
}
?>
<table border="0" width="95%" cellspacing="0" cellpadding="2">
	<tr class="dataTableHeadingRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_ID'); ?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE'); ?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_MOVIE_TITLE'); ?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_SHIPPED');?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_RECEIVED');?></td>
		<td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_STATUS');?></td>
	</tr>
	<?php echo implode("\n", $templateParsed);?>
</table>
