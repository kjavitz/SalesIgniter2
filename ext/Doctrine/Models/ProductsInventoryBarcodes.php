<?php
/*
	Products Inventory Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsInventoryBarcodes extends Doctrine_Record {

	public function setUp(){
		parent::setUp();

		$this->hasOne('ProductsInventory as Inventory', array(
			'local'   => 'inventory_id',
			'foreign' => 'inventory_id'
		));

		$this->hasOne('SystemStatuses as Status', array(
			'local'   => 'status_id',
			'foreign' => 'status_id'
		));

		$this->hasMany('ProductsInventoryBarcodesComments', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('products_inventory_barcodes');
		
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('inventory_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('barcode', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false,
		));

		$this->hasColumn('status_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('attributes', 'string', 999, array(
			'type'          => 'string',
			'length'        => 999,
			'primary'       => false,
			'default'       => null,
			'notnull'       => false,
			'autoincrement' => false,
		));
	}
}