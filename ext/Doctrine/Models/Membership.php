<?php

/**
 * Membership
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class Membership extends Doctrine_Record {
	
	public function setUp(){
		$this->hasMany('MembershipPlanDescription', array(
			'local' => 'plan_id',
			'foreign' => 'plan_id',
			'cascade' => array('delete')
		));
		$this->hasOne('TaxRates', array(
			'local' => 'rent_tax_class_id',
			'foreign' => 'tax_rates_id'
		));
		$this->setUpParent();
	}
	
	public function setUpParent(){
		$CustomersMembership = Doctrine_Core::getTable('CustomersMembership')->getRecordInstance();
		
		$CustomersMembership->hasOne('Membership', array(
			'local' => 'plan_id',
			'foreign' => 'plan_id'
		));
	}
	
	public function preInsert($event){
		$this->date_added = date('Y-m-d');
	}
	
	public function preUpdate($event){
		$this->last_modified = date('Y-m-d');
	}
	
	public function setTableDefinition(){
		$this->setTableName('membership');
		
		$this->hasColumn('plan_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => true,
		'autoincrement' => true,
		));
		$this->hasColumn('membership_months', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('membership_days', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('no_of_titles', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('price', 'decimal', 9, array(
		'type' => 'decimal',
		'length' => 9,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0.0000',
		'notnull' => true,
		'autoincrement' => false,
		'scale' => 4,
		));
		$this->hasColumn('rent_tax_class_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => false,
		'autoincrement' => false,
		));
		$this->hasColumn('date_added', 'timestamp', null, array(
		'type' => 'timestamp',
		'primary' => false,
		'default' => '0000-00-00 00:00:00',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('last_modified', 'timestamp', null, array(
		'type' => 'timestamp',
		'primary' => false,
		'default' => '0000-00-00 00:00:00',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('free_trial', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('free_trial_flag', 'enum', 1, array(
		'type' => 'enum',
		'length' => 1,
		'fixed' => false,
		'values' =>
		array(
		0 => 'Y',
		1 => 'N',
		),
		'primary' => false,
		'default' => 'N',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('free_trial_amount', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('payment_term', 'string', 2, array(
		'type' => 'string',
		'length' => 2,
		'fixed' => true,
		'primary' => false,
		'default' => 'M',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('sort_order', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('default_plan', 'integer', 1, array(
		'type' => 'integer',
		'length' => 1,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
	}
}