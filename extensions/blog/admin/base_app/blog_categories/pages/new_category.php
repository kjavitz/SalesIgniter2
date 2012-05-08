<?php
/*
	Blog Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Categories = Doctrine_Core::getTable('BlogCategories');
	if (isset($_GET['cID']) && empty($_POST)){
		$Category = $Categories->findOneByBlogCategoriesId((int)$_GET['cID']);
		$Category->refresh(true);
	}else{
		$Category = $Categories->getRecord();
	}
?>
<form name="new_category" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=saveCategory');?>" method="post" enctype="multipart/form-data">
<div id="tab_container">
 <ul>
<li class="ui-tabs-nav-item"><a href="#page-1"><span><?php echo sysLanguage::get('TAB_GENERAL');?></span></a></li>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>

<?php
	$contents = EventManager::notifyWithReturn('NewBlogCategoryTabHeader');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?> 
 </ul>

<div id="page-1"><?php include(sysConfig::getDirFsCatalog(). 'extensions/blog/admin/base_app/blog_categories/pages_tabs/tab_general.php');?></div>
 <div id="page-2"><?php include(sysConfig::getDirFsCatalog(). 'extensions/blog/admin/base_app/blog_categories/pages_tabs/tab_description.php');?></div>
<?php
	$contents = EventManager::notifyWithReturn('NewBlogCategoryTabBody', &$Category);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>
