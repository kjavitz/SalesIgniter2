<?php
/*
	Products Inventory Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ProductsInventoryBarcodesComments extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$ProductsInventoryBarcodes = Doctrine::getTable('ProductsInventoryBarcodes')->getRecordInstance();
		
		$ProductsInventoryBarcodes->hasMany('ProductsInventoryBarcodesComments', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id',
			'cascade' => array('delete')
		));
	}
	
	public function preInsert($event){
		$this->date_added = date('Y-m-d');
	}
	
	public function preUpdate($event){
	}

	public function setTableDefinition(){
		$this->setTableName('products_inventory_barcodes_comments');
		
		$this->hasColumn('comments_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('comments', 'string', null, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('date_added', 'date', null, array(
			'type'          => 'date',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
}