<?php

/**
 * OrdersPaymentsHistory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class OrdersPaymentsHistory extends Doctrine_Record {

	public function preInsert($event){
		$this->date_added = date(DATE_TIMESTAMP);
	}
	
	public function preUpdate($event){
	}

	public function setTableDefinition(){
		$this->setTableName('orders_payments_history');
		
		$this->hasColumn('payment_history_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		$this->hasColumn('orders_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('date_added', 'timestamp');
		$this->hasColumn('gateway_message', 'string', 999, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('payment_amount', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));
		$this->hasColumn('payment_module', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('payment_method', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('card_details', 'string', 999, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('success', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0
		));
		$this->hasColumn('can_reuse', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0
		));
		$this->hasColumn('is_refund', 'integer', 1, array(
			'type' => 'integer',
			'default' => '0',
			'length' => 1,
			'unsigned' => 0
		));
	}
}