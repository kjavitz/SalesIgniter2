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
	case 'default':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE_DEFAULT'));
		break;
	case 'new':
		sysLanguage::set('PAGE_TITLE', sysLanguage::get('HEADING_TITLE_NEW'));
		break;
}
