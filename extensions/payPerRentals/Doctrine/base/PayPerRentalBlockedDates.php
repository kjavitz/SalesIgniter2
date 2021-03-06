<?php
/*
Pay Per Rental Products Extension Version 1

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class PayPerRentalBlockedDates extends Doctrine_Record {
	
	public function setUp(){

	}

	
	public function setTableDefinition(){
		$this->setTableName('pay_per_rental_blocked_dates');
		
		$this->hasColumn('block_dates_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('block_start_date', 'datetime', null, array(
			'type'          => 'datetime',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('block_end_date', 'datetime', null, array(
			'type'          => 'datetime',
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false
		));

		$this->hasColumn('block_name', 'string', 128, array(
			'type'          => 'string',
			'length'        => 128,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

		$this->hasColumn('recurring', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'primary'       => false,
			'notnull'       => true,
			'autoincrement' => false,
		));

	}
}