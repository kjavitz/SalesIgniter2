<?php
/*
	Customers Queue Table

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomersQueue extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		$this->setUpParent();
	}

	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();

		$Customers->hasOne('CustomersBasket', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('customers_queue');

		$this->hasColumn('customers_queue_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true,
		));

		$this->hasColumn('customers_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('queue_data', 'string', 999, array(
			'type'          => 'string',
			'length'        => 999
		));
	}
}