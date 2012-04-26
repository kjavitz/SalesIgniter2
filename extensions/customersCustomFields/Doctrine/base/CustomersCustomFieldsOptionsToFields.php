<?php
/*
	Customers Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class CustomersCustomFieldsOptionsToFields extends Doctrine_Record {
	
	public function setUp(){
		$this->hasOne('CustomersCustomFields as Field', array(
			'local' => 'field_id',
			'foreign' => 'field_id'
		));
		
		$this->hasOne('CustomersCustomFieldsOptions as Options', array(
			'local' => 'option_id',
			'foreign' => 'option_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('customers_custom_fields_options_to_fields');
		
		$this->hasColumn('option_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('field_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
	}
}