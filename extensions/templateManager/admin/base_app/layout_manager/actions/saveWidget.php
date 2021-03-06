<?php
$success = false;
$Widget = $LayoutBuilder->getWidget($_GET['widgetCode']);

if ($Widget !== false){
	$WidgetProperties = array(
		'id' => $_POST['id'],
		'template_file' => $_POST['template_file'],
		'widget_title' => $_POST['widget_title'],
		'widget_title_link' => $_POST['widget_title_link']
	);

	$WidgetPreview = false;
	if (file_exists($Widget->getPath() . 'actions/saveWidget.php')){
		require($Widget->getPath() . 'actions/saveWidget.php');

		if (method_exists($Widget, 'showLayoutPreview')){
			$WidgetPreview = $Widget->showLayoutPreview(array(
				'settings' => json_decode(json_encode($WidgetProperties))
			));
		}
	}
	$success = true;
}

EventManager::attachActionResponse(array(
	'success' => $success,
	'widgetSettings' => ($success === true ? $WidgetProperties : ''),
	'widgetPreview' => ($success === true ? $WidgetPreview : '')
), 'json');
?>