<?php
if (isset($_POST['facebook'])){
	$WidgetProperties['facebook'] = $_POST['facebook'];
}

if (isset($_POST['youtube'])){
	$WidgetProperties['youtube'] = $_POST['youtube'];
}

if (isset($_POST['twitter'])){
	$WidgetProperties['twitter'] = $_POST['twitter'];
}
if (isset($_POST['linked'])){
	$WidgetProperties['linked'] = $_POST['linked'];
}
if (isset($_POST['beforeText'])){
	$WidgetProperties['beforeText'] = $_POST['beforeText'];
}
if (isset($_POST['email'])){
	$WidgetProperties['email'] = $_POST['email'];
}
?>