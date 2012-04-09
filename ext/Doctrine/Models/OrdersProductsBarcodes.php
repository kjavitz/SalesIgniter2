<?php

/**
 * OrdersProducts
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class OrdersProductsBarcodes extends Doctrine_Record {

	public function setUp(){
		parent::setUp();

		$this->hasOne('ProductsInventoryBarcodes', array(
			'local' => 'barcode_id',
			'foreign' => 'barcode_id'
		));
	}

	public function preDelete($event){
		Doctrine_Query::create()
			->update('ProductsInventoryBarcodes')
			->set('status', '?', 'A')
			->where('barcode_id = ?', $this->barcode_id)
			->execute();
	}

	public function setTableDefinition(){
		$this->setTableName('orders_products_barcodes');
		$this->hasColumn('orders_products_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}