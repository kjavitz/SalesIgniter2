<?php
/*
	Orders Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class OrdersCustomFieldsToOrders extends Doctrine_Record {
	
	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('OrdersCustomFields as Field', array(
			'local' => 'field_id',
			'foreign' => 'field_id'
		));
	}

	public function setUpParent(){
		$Orders = Doctrine::getTable('Orders')->getRecordInstance();
		
		$Orders->hasMany('OrdersCustomFieldsToOrders as CustomFields', array(
			'local' => 'orders_id',
			'foreign' => 'orders_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('orders_custom_fields_to_orders');
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('orders_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('value', 'string', 999, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('field_type', 'string', 16, array(
			'type' => 'string',
			'length' => 16,
			'fixed' => false,
			'primary' => false,
			'default' => 'text',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('field_label', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('field_identifier', 'string', 255, array(
			'type' => 'string',
			'length' => 255,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}