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

switch($App->getPageName()){
	case 'new_post':
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/adapters/jquery.js');
		if (isset($_GET['pID'])){
			$headingTitle = 'Edit Post';
		}else{
			$headingTitle = 'New Post';
		}
		sysLanguage::set('PAGE_TITLE', $headingTitle);
		break;
	case 'new_comment':
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/adapters/jquery.js');
		if (isset($_GET['cID'])){
			$headingTitle = 'Edit Comment';
		}else{
			$headingTitle = 'New Comment';
		}
		sysLanguage::set('PAGE_TITLE', $headingTitle);
		break;
	case 'default':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE'));
		break;
}
