<?php

/**
 * ProductsPurchaseTypes
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 */
class ProductsPurchaseTypes extends Doctrine_Record
{

	public function setUp()
	{
		parent::setUp();

		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'type_name');
		$this->hasOne('Products', array(
			'local'   => 'products_id',
			'foreign' => 'products_id'
		));

		$this->hasMany('ProductsInventoryItems as InventoryItems', array(
			'local'   => 'purchase_type_id',
			'foreign' => 'purchase_type_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition()
	{
		$this->setTableName('products_purchase_types');

		$this->hasColumn('purchase_type_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'fixed'         => false,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => true
		));

		$this->hasColumn('products_id', 'integer', 4);
		$this->hasColumn('status', 'integer', 1, array(
			'default' => '0'
		));
		$this->hasColumn('type_name', 'string', 32);
		$this->hasColumn('price', 'decimal', 15, array(
			'default' => '0.0000',
			'scale'   => 4
		));
		$this->hasColumn('tax_class_id', 'integer', 4, array(
			'default' => '0'
		));
		$this->hasColumn('use_serials', 'integer', 1, array(
			'default' => '0'
		));
	}
}
