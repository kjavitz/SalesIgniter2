<?php
	$Event = Doctrine_Core::getTable('EventManagerEvents');
	if (isset($_GET['eID']) && empty($_POST)){
		$Event = $Event->findOneByEventsId((int)$_GET['eID']);
		//$Inventory->refresh(true);
	}else{
		$Event = $Event->getRecord();
	}

?>
<form name="new_event" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=save');?>" method="post" enctype="multipart/form-data">
<div id="tab_container">
 <ul>
  <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
 </ul>

 <div id="page-2"><?php include(sysConfig::getDirFsCatalog() . 'extensions/eventManager/admin/base_app/manage/pages_tabs/tab_description.php');?></div>

</div>
<br />
<div style="text-align:right"><?php
   $saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
   $cancelButton = htmlBase::newElement('button')->usePreset('cancel')
   ->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

   echo $saveButton->draw() . $cancelButton->draw();
?></div>
</form>