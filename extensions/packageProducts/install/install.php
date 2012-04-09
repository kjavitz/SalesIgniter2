<?php
/*
	Package Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class packageProductsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('packageProducts');
	}

	public function install(){
		if (sysConfig::exists('EXTENSION_PACKAGE_PRODUCTS_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_PACKAGE_PRODUCTS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
