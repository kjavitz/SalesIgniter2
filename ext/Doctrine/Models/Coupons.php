<?php

/**
 * Coupons
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class Coupons extends Doctrine_Record {
	
	public function preInsert($event){
		$this->date_created = date('Y-m-d');
	}
	
	public function preUpdate($event){
		$this->date_modified = date('Y-m-d');
	}

    public function setUp(){
		$this->hasMany('CouponsDescription', array(
			'local'   => 'coupon_id',
			'foreign' => 'coupon_id',
			'cascade' => array('delete')
		));
		$this->hasOne('CouponEmailTrack',array(
			'local'	=>	'coupon_id',
			'foreign'	=> 'coupon_id'
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('coupons');
		
		$this->hasColumn('coupon_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('coupon_type', 'string', 1, array(
			'type' => 'string',
			'length' => 1,
			'fixed' => true,
			'primary' => false,
			'default' => 'F',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('coupon_code', 'string', 32, array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'primary' => false,
			'default' => '',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('coupon_amount', 'decimal', 8, array(
			'type' => 'decimal',
			'length' => 8,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));
		
		$this->hasColumn('coupon_minimum_order', 'decimal', 8, array(
			'type' => 'decimal',
			'length' => 8,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));
		$this->hasColumn('coupon_maximum_order', 'decimal', 8, array(
			'type' => 'decimal',
			'length' => 8,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));
		
		$this->hasColumn('coupon_start_date', 'timestamp', null, array(
			'type' => 'timestamp',
			'primary' => false,
			'default' => '0000-00-00 00:00:00',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('coupon_expire_date', 'timestamp', null, array(
			'type' => 'timestamp',
			'primary' => false,
			'default' => '0000-00-00 00:00:00',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('uses_per_coupon', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '1',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('uses_per_user', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('number_days_membership', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));

		$this->hasColumn('coupon_products_use', 'string', '64');
		$this->hasColumn('coupon_products', 'string', 999);
		
		$this->hasColumn('restrict_to_products', 'string', 255, array(
			'type' => 'string',
			'length' => 255,
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('restrict_to_categories', 'string', 255, array(
			'type' => 'string',
			'length' => 255,
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('restrict_to_customers', 'string', 999, array(
			'type' => 'string',
			'fixed' => false,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('coupon_active', 'string', 1, array(
			'type' => 'string',
			'length' => 1,
			'fixed' => true,
			'primary' => false,
			'default' => 'Y',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('date_created', 'timestamp', null, array(
			'type' => 'timestamp',
			'primary' => false,
			'default' => '0000-00-00 00:00:00',
			'notnull' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('date_modified', 'timestamp', null, array(
			'type' => 'timestamp',
			'primary' => false,
			'default' => '0000-00-00 00:00:00',
			'notnull' => true,
			'autoincrement' => false,
		));
	}
}