<?php
	require(sysConfig::getDirFsCatalog() . 'includes/classes/ProductBase.php');

	function addGridRow($productClass, &$tableGrid, &$infoBoxes){
		global $allGetParams, $editButton, $deleteButton, $currencies;
		$productId = $productClass->getID();
		$productModel = $productClass->getModel();
		$productName = $productClass->getName();
		if(sysConfig::exists('EXTENSION_REVIEWS_ENABLED') && sysConfig::get('EXTENSION_REVIEWS_ENABLED') == 'True') {
			$Qreviews = Doctrine_Query::create()
			->select('(avg(reviews_rating) / 5 * 100) as average_rating')
			->from('Reviews r')
			->where('r.products_id = ?', $productId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$reviews = $Qreviews[0];
		}
		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref(itw_app_link($allGetParams . 'pID=' . $productId));

		$statusIcon = htmlBase::newElement('icon');
		if ($productClass->isActive() === true){
			$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
			->setHref(itw_app_link($allGetParams . 'action=setflag&flag=0&pID=' . $productId));
		}else{
			$statusIcon->setType('circleClose')->setTooltip('Click to enable')
			->setHref(itw_app_link($allGetParams . 'action=setflag&flag=1&pID=' . $productId));
		}

		$featuredIcon = htmlBase::newElement('icon');
		if ($productClass->isFeatured() === true){
			$featuredIcon->setType('circleCheck')->setTooltip('Click to disable')
			->setHref(itw_app_link($allGetParams . 'action=setfflag&fflag=0&pID=' . $productId));
		}else{
			$featuredIcon->setType('circleClose')->setTooltip('Click to enable')
			->setHref(itw_app_link($allGetParams . 'action=setfflag&fflag=1&pID=' . $productId));
		}

		$nameAlignCenter = false;
		if (empty($productName)){
			$nameAlignCenter = true;
			$productName = htmlBase::newElement('icon')->setType('alert')->setTooltip('This product needs a name')->draw();
			$nameSpacing = '';
		}

		$modelAlignCenter = false;
		if (empty($productModel)){
			$modelAlignCenter = true;
			$productModel = htmlBase::newElement('icon')->setType('alert')->setTooltip('This product needs a model to work with data export/import')->draw();
		}

		$rowAttr = array('infobox_id' => $productId);
		$nameSpacing = '';
		if ($productClass->isInBox() === true){
			$rowAttr['box_id'] = $productClass->getBoxID();
			$rowAttr['style'] = 'display:none';
			$nameSpacing = '&nbsp;|-&nbsp;';
		}

		/*
		$purchaseTypeNew = $productClass->getPurchaseType('new', true);
		$purchaseTypeUsed = $productClass->getPurchaseType('used', true);
		$purchaseTypeReservation = $productClass->getPurchaseType('reservation', true);
		$purchaseTypeRental = $productClass->getPurchaseType('rental', true);
		*/
		
		$bodyRow = array();
		$bodyRow[] = array('text' => ($productClass->isBox() === true ? htmlBase::newElement('icon')->addClass('setExpander')->setType('triangleEast')->draw() : '&nbsp;'), 'align' => 'center');
		$bodyRow[] = array('text' => $productId, 'format' => 'int');
		$bodyRow[] = array('text' => $nameSpacing . $productName, 'align' => ($nameAlignCenter === true ? 'center' : 'left'));
		$bodyRow[] = array('text' => $productModel, 'align' => ($modelAlignCenter === true ? 'center' : 'left'));
		
		$added = 0;
		foreach(PurchaseTypeModules::getModules() as $PurchaseType){
			if ($PurchaseType->getConfigData('SHOW_ON_ADMIN_PRODUCT_LIST') == 'True'){
				$PurchaseType->loadProduct($productClass);
				$bodyRow[] = array('text' => (int) $PurchaseType->getCurrentStock(), 'align' => 'center', 'format' => 'int');
				$added++;
			}
		}
		
		$bodyRow[] = array('text' => '<a href="'. itw_app_link($allGetParams . 'pID='.$productId, null, 'new_product').'#page-4">'.'[View/Edit/Add]'.'</a>', 'align' => 'center');
		$bodyRow[] = array('text' => $statusIcon->draw(), 'align' => 'center');
		$bodyRow[] = array('text' => $featuredIcon->draw(), 'align' => 'center');
		$bodyRow[] = array('text' => $arrowIcon->draw(), 'align' => 'right');
			
		$tableGrid->addBodyRow(array(
			'rowAttr' => $rowAttr,
			'columns' => $bodyRow
		));

		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setButtonBarLocation('top');

		$infoBox->setHeader('<b>' . $productClass->getName() . '</b>');

		$editButton->setHref(itw_app_link(tep_get_all_get_params(array('pID')). 'pID=' . $productId, null, 'new_product'));
		$deleteButton->attr('products_id', $productId);

		$infoBox->addButton($editButton)->addButton($deleteButton);

		$infoBox->setForm(array(
			'name' => 'generate',
			'action' => itw_app_link('action=generateProducts')
		));

		if ($productClass->isInBox() === false){
			$infoBox->addContentRow('<table cellpadding="3" cellspacing="0">' .
				'<tr>' .
					'<td class="smallText" colspan="2">' . sysLanguage::get('TEXT_BOX_SET_TITLE') . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td class="smallText">' . sysLanguage::get('TEXT_BOX_SET_BODY') . '</td>' .
					'<td class="smallText">' . tep_draw_input_field('discs','','size=5') . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td class="smallText" colspan="2">' . htmlBase::newElement('button')->setText('Generate Box Set')->setType('submit')->draw() . '</td>' .
				'</tr>' .
				'<tr>' .
					'<td class="smallText" colspan="2"><br>' . sysLanguage::get('TEXT_BOX_SET_FOTTER') . tep_draw_hidden_field('products_id', $productId) . '</td>' .
				'</tr>' .
			'</table>');
		}

		$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_ADDED') . ' ' . $productClass->getDateAdded()->format(DATE_RSS));
		if (tep_not_null($productClass->getLastModified())){
			$infoBox->addContentRow(sysLanguage::get('TEXT_LAST_MODIFIED') . ' ' . $productClass->getLastModified()->format(DATE_RSS));
		}

		if (date('Y-m-d') < $productClass->getDateAvailable()){
			$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_AVAILABLE') . ' ' . $productClass->getDateAvailable()->format(DATE_RSS));
		}

		$productImage = $productClass->getImage();
		if (!empty($productImage) && file_exists($_SERVER['DOCUMENT_ROOT'] . $productImage)){
			$imageHtml = htmlBase::newElement('image')
			->setSource($productImage)
			->setWidth(SMALL_IMAGE_WIDTH)
			->setHeight(SMALL_IMAGE_HEIGHT)
			->thumbnailImage(true);
		}else{
			$imageHtml = htmlBase::newElement('span')
			->addClass('main')
			->html('Image Does Not Exist');
		}

		$infoBox->addContentRow($imageHtml->draw() . '<br />' . $productImage);
		//$infoBox->addContentRow(sysLanguage::get('TEXT_PRODUCTS_PRICE_INFO') . ' ' . $currencies->format($purchaseTypeNew->getPrice()));
		if(sysConfig::exists('EXTENSION_REVIEWS_ENABLED') && sysConfig::get('EXTENSION_REVIEWS_ENABLED') == 'True') {
			$infoBox->addContentRow(sysLanguage::get('TEXT_PRODUCTS_AVERAGE_RATING') . ' ' . number_format($reviews['average_rating'], 2) . '%');
		}

		$infoBoxes[$productId] = $infoBox->draw();
		unset($infoBox);

		if ($productClass->isBox() === true){
			$discs = $productClass->getDiscs(false, true);
			foreach($discs as $setProductId){
				$setProductClass = new product($setProductId);
				addGridRow($setProductClass, &$tableGrid, &$infoBoxes);
			}
		}
	}

	$rows = 0;
	$products_count = 0;
	$lID = (int)Session::get('languages_id');

	$Qproducts = Doctrine_Query::create()
	->select('p.products_id, pd.products_name, p2c.categories_id')
	->from('Products p')
	->leftJoin('p.ProductsDescription pd')
	->leftJoin('p.ProductsToCategories p2c')
	->where('pd.language_id = ?', $lID)
	->andWhere('p.products_in_box = ?', '0')
	->orderBy('p.products_featured desc, pd.products_name asc, p.products_id desc');
	if (isset($_GET['search'])) {
		$search = tep_db_prepare_input($_GET['search']);
		$Qproducts->andWhere('pd.products_name LIKE ?', '%' . $search . '%');
	}

	if(isset($_GET['categorySelect']) && $_GET['categorySelect'] != -1){
		$Qproducts->andWhere('p2c.categories_id=?', $_GET['categorySelect']);
	}

	EventManager::notify('AdminProductListingQueryBeforeExecute', &$Qproducts);

    /*update pay per rentals with the new fields this can be removed after update
    $QproductsUpdate = Doctrine_Query::create()
	->from('ProductsPayPerRental ppr')
	->execute();
    $PricePerRentalPerProducts = Doctrine_Core::getTable('PricePerRentalPerProducts');
	foreach($QproductsUpdate as $iProducts){
		    //DAILY
			if($iProducts['price_daily'] > 0){
				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				foreach(sysLanguage::getLanguages() as $lInfo){
					$Description[$lInfo['id']]->language_id = $lInfo['id'];
					$Description[$lInfo['id']]->price_per_rental_per_products_name = sysLanguage::get('PPR_DAILY_PRICE');
				}
				$PricePerProduct->price = $iProducts['price_daily'];
				$PricePerProduct->number_of = 1;
				$PricePerProduct->pay_per_rental_types_id = 3;
				$PricePerProduct->pay_per_rental_id = $iProducts['pay_per_rental_id'];
				$PricePerProduct->save();
				$iProducts->price_daily = 0;
			}

			//WEEKLY
			if($iProducts['price_weekly'] > 0){
				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				foreach(sysLanguage::getLanguages() as $lInfo){
					$Description[$lInfo['id']]->language_id = $lInfo['id'];
					$Description[$lInfo['id']]->price_per_rental_per_products_name = sysLanguage::get('PPR_WEEKLY_PRICE');
				}
				$PricePerProduct->price = $iProducts['price_weekly'];
				$PricePerProduct->number_of = 1;
				$PricePerProduct->pay_per_rental_types_id = 4;
				$PricePerProduct->pay_per_rental_id = $iProducts['pay_per_rental_id'];
				$PricePerProduct->save();
				$iProducts->price_weekly = 0;
			}

			//MONTHLY
			if($iProducts['price_monthly'] > 0){
				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				foreach(sysLanguage::getLanguages() as $lInfo){
					$Description[$lInfo['id']]->language_id = $lInfo['id'];
					$Description[$lInfo['id']]->price_per_rental_per_products_name = sysLanguage::get('PPR_MONTHLY_PRICE');
				}
				$PricePerProduct->price = $iProducts['price_monthly'];
				$PricePerProduct->number_of = 1;
				$PricePerProduct->pay_per_rental_types_id = 5;
				$PricePerProduct->pay_per_rental_id = $iProducts['pay_per_rental_id'];
				$PricePerProduct->save();
				$iProducts->price_monthly = 0;
			}

		   //6 MONTHS
			if($iProducts['price_six_month'] > 0){
				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				foreach(sysLanguage::getLanguages() as $lInfo){
					$Description[$lInfo['id']]->language_id = $lInfo['id'];
					$Description[$lInfo['id']]->price_per_rental_per_products_name = sysLanguage::get('PPR_6_MONTHS_PRICE');
				}
				$PricePerProduct->price = $iProducts['price_six_month'];
				$PricePerProduct->number_of = 6;
				$PricePerProduct->pay_per_rental_types_id = 5;
				$PricePerProduct->pay_per_rental_id = $iProducts['pay_per_rental_id'];
				$PricePerProduct->save();
				$iProducts->price_six_month = 0;
			}

		   //1 YEAR
			if($iProducts['price_year'] > 0){
				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				foreach(sysLanguage::getLanguages() as $lInfo){
					$Description[$lInfo['id']]->language_id = $lInfo['id'];
					$Description[$lInfo['id']]->price_per_rental_per_products_name = sysLanguage::get('PPR_1_YEAR_PRICE');
				}
				$PricePerProduct->price = $iProducts['price_year'];
				$PricePerProduct->number_of = 1;
				$PricePerProduct->pay_per_rental_types_id = 6;
				$PricePerProduct->pay_per_rental_id = $iProducts['pay_per_rental_id'];
				$PricePerProduct->save();
				$iProducts->price_year = 0;
			}

		  //3 YEAR
			if($iProducts['price_three_year'] > 0){
				$PricePerProduct = $PricePerRentalPerProducts->create();
				$Description = $PricePerProduct->PricePayPerRentalPerProductsDescription;
				foreach(sysLanguage::getLanguages() as $lInfo){
					$Description[$lInfo['id']]->language_id = $lInfo['id'];
					$Description[$lInfo['id']]->price_per_rental_per_products_name = sysLanguage::get('PPR_3_YEAR_PRICE');
				}
				$PricePerProduct->price = $iProducts['price_three_year'];
				$PricePerProduct->number_of = 3;
				$PricePerProduct->pay_per_rental_types_id = 6;
				$PricePerProduct->pay_per_rental_id = $iProducts['pay_per_rental_id'];
				$PricePerProduct->save();
				$iProducts->price_three_year = 0;
			}
			if($iProducts['max_days'] > 0){
				$iProducts->max_period = $iProducts['max_days'];
				$iProducts->max_type = 3;
			}
			if($iProducts['max_months'] > 0){
				$iProducts->max_period = $iProducts['max_months'];
				$iProducts->max_type = 5;
			}
			if($iProducts['min_rental_days'] > 0){
				$iProducts->min_period = $iProducts['min_rental_days'];
				$iProducts->min_type = 3;
			}
		$iProducts->save();

	}
    end of update*/
	$tableGrid = htmlBase::newElement('grid')
	->usePagination(true)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($Qproducts);

	$header2 = array();
	$header2[] = array('text' => 'Set');
	$header2[] = array('text' => sysLanguage::get('TABLE_HEADING_ID'));
	$header2[] = array('text' => 'Name');
	$header2[] = array('text' => 'Model');
	
	$stockColSpan = 0;
	foreach(PurchaseTypeModules::getModules() as $PurchaseType){
		if ($PurchaseType->getConfigData('SHOW_ON_ADMIN_PRODUCT_LIST') == 'True'){
			$header2[] = array('text' => $PurchaseType->getTitle());
			$stockColSpan++;
		}
	}
	$header2[] = array('text' => '&nbsp;');
	$header2[] = array('colspan' => 4);
		
	$header1 = array();
	$header1[] = array('colspan' => 4, 'text' => sysLanguage::get('TABLE_HEADING_PRODUCTS'));
	if ($stockColSpan > 0){
		$header1[] = array('colspan' => $stockColSpan, 'text' => 'Stock');
	}
	$header1[] = array('text' => sysLanguage::get('TABLE_HEADING_VIEW_COMMENTS'));
	$header1[] = array('text' => sysLanguage::get('TABLE_HEADING_STATUS'));
	$header1[] = array('text' => sysLanguage::get('TABLE_HEADING_FEATURED'));
	$header1[] = array('text' => sysLanguage::get('TABLE_HEADING_ACTION'));

	$tableGrid->addHeaderRow(array('columns' => $header1));
	$tableGrid->addHeaderRow(array('columns' => $header2));

	$products = &$tableGrid->getResults();
	$infoBoxes = array();
	if ($products){
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete')->addClass('deleteProductButton');

		$allGetParams = tep_get_all_get_params(array('action', 'pID', 'flag', 'fflag'));
		foreach($products as $product){
			$ProductClass = new Product((int) $product['products_id']);
			addGridRow($ProductClass, $tableGrid, $infoBoxes);
			
			/*
			$productId = (int)$product['products_id'];
			$productClass = new product($productId);

			addGridRow($productClass, $tableGrid, $infoBoxes);
			*/
		}
	}
	$array_limit = array(
		array(
			'id'   => '10',
			'text' => '10'
		),
		array(
			'id'   => '25',
			'text' => '25'
		),
		array(
			'id'   => '50',
			'text' => '50'
		),
		array(
			'id'   => '100',
			'text' => '100'
		)
	);
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php
	   $searchForm = htmlBase::newElement('form')
		   ->attr('name', 'search')
		   ->attr('id', 'search')
		   ->attr('action', itw_app_link(null, null, null, 'SSL'))
		   ->attr('method', 'get');

	   $pageLimit = htmlBase::newElement('selectbox')
			   ->setName('limit')
			   ->setId('limit')
			   ->setLabel(sysLanguage::get('TEXT_SEARCH_RESULTS'))
			   ->setLabelPosition('before');
	   foreach($array_limit as $limitOption){
		   $pageLimit->addOption($limitOption['id'], $limitOption['text']);
	   }
	   $pageLimit->selectOptionByValue(isset($_GET['limit']) ? $_GET['limit'] : '10');

    $categorySelect = htmlBase::newElement('selectbox')
	->setName('categorySelect')
	->setLabel(sysLanguage::get('TEXT_SELECT_CATEGORY'))
    ->setLabelPosition('before');

   	function addCategoryTreeToGrid($parentId, &$categorySelect, $namePrefix = ''){
		global $lID, $allGetParams, $cInfo;
		$Qcategories = Doctrine_Query::create()
		->select('c.*, cd.categories_name')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('cd.language_id = ?', $lID)
		->andWhere('c.parent_id = ?', $parentId)
		->orderBy('c.sort_order, cd.categories_name');

		EventManager::notify('CategoryListingQueryBeforeExecute', &$Qcategories);

		$ResultC = $Qcategories->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if (count($ResultC) > 0){
			foreach($ResultC as $Category){

				$categorySelect->addOption($Category['categories_id'], $namePrefix. $Category['CategoriesDescription'][0]['categories_name']);
				addCategoryTreeToGrid($Category['categories_id'], &$categorySelect, '&nbsp;&nbsp;&nbsp;' . $namePrefix);
			}
		}
	}
    $categorySelect->addOption('-1', sysLanguage::get('TEXT_PLEASE_SELECT'));
	addCategoryTreeToGrid(0, $categorySelect,'');
    if(isset($_GET['categorySelect']) && $_GET['categorySelect'] != -1){
	    $categorySelect->selectOptionByValue($_GET['categorySelect']);
    }

   $searchField = htmlBase::newElement('input')
			   ->setName('search')
			   ->setLabel(sysLanguage::get('HEADING_TITLE_SEARCH'))
			   ->setLabelPosition('before');
   if (isset($_GET['search'])){
   	$searchField->setValue($_GET['search']);
   }
   $searchForm->append($pageLimit);

   $searchForm->append($searchField)->append($categorySelect);

   $contents = EventManager::notify('ProductsDefaultAddFilterOptions', &$searchForm);

   echo $searchForm->draw();
   ?></td>
  </tr>
 </table>

 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   <?php
	   $formPages = htmlBase::newElement('form')
		            ->attr('action', itw_app_link(null,'products','default'))
                    ->attr('id', 'form_page')
		            ->attr('method', 'get');
       $pageSelect = htmlBase::newElement('selectbox')
	                    ->setName('page')
                        ->setId('select_page')
	                    ->setLabel('Go To')
                        ->setLabelPosition('before');

	   for($i=1;$i<=$tableGrid->pager->getLastPage();$i++){
		   $pageSelect->addOption($i, 'Page '. $i);
	   }
	   if (isset($_GET['page'])){
	        $pageSelect->selectOptionByValue($_GET['page']);
	   }
	   $formPages->append($pageSelect);
	   echo $formPages->draw();
	?>

   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
    if (!isset($_GET['search'])){
	    $ProductTypesMenu = htmlBase::newElement('selectbox')
		    ->setName('productType')
	        ->setId('newProductType');
	    ProductTypeModules::loadModules();
	    foreach(ProductTypeModules::getModules() as $ModuleCode => $ModuleCls){
		    $ProductTypesMenu->addOption($ModuleCls->getCode(), $ModuleCls->getTitle());
	    }
	    $newProdButton = htmlBase::newElement('button')
	        ->setId('newProductButton')
		    ->usePreset('install')
		    ->setText(sysLanguage::get('TEXT_BUTTON_NEW_PRODUCT'));

    	echo $ProductTypesMenu->draw() . '&nbsp;' . $newProdButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $pID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $pID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>