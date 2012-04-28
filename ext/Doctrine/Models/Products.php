<?php

/**
 * Products
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class Products extends Doctrine_Record {

	public function setUp(){
		parent::setUp();
		
		$this->hasMany('ProductsDescription', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsToBox', array(
			'local'   => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsToCategories', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsAdditionalImages', array(
			'local' => 'products_id',
			'foreign' => 'products_id',
			'cascade' => array('delete')
		));
		
		$this->hasMany('ProductsPurchaseTypes', array(
			'local' => 'products_id',
			'foreign' => 'products_id'
		));

		$this->hasOne('TaxClass', array(
			'local' => 'products_tax_class_id',
			'foreign' => 'tax_class_id'
		));
	}
	
	public function preInsert($event){
		$this->products_date_added->setTimestamp(time());
		$this->products_last_modified = null;
	}
	
	public function preUpdate($event){
		if ($this->products_last_modified instanceof SesDateTime){
			$this->products_last_modified->setTimestamp(time());
		}else{
			$this->products_last_modified = date(DATE_TIMESTAMP);
		}
	}
	
	public function setTableDefinition(){
		$this->setTableName('products');

		$this->hasColumn('products_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		$this->hasColumn('products_model', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		$this->hasColumn('products_image', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		$this->hasColumn('products_keepit_price', 'decimal', 15, array(
				'type'          => 'decimal',
				'length'        => 15,
				'unsigned'      => 0,
				'primary'       => false,
				'default'       => '0.0000',
				'notnull'       => true,
				'autoincrement' => false,
				'scale'         => false
		));
		$this->hasColumn('products_date_added', 'timestamp');
		$this->hasColumn('products_last_modified', 'timestamp');
		$this->hasColumn('products_date_available', 'timestamp');
		$this->hasColumn('products_weight', 'decimal', 5, array(
			'type'          => 'decimal',
			'length'        => 5,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.00',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 2
		));
		$this->hasColumn('products_status', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('products_tax_class_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('products_ordered', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('products_type', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'default'       => 'standard',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('products_in_box', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
        $this->hasColumn('products_on_order', 'integer', 1, array(
            'type'          => 'integer',
            'length'        => 1,
            'unsigned'      => 0,
            'primary'       => false,
            'default'       => '0',
            'notnull'       => true,
            'autoincrement' => false
        ));
        $this->hasColumn('products_display_order', 'integer', 2, array(
            'type'          => 'integer',
            'length'        => 2,
            'unsigned'      => 0,
            'primary'       => false,
            'default'       => '0',
            'notnull'       => true,
            'autoincrement' => false
        ));
        $this->hasColumn('products_date_ordered', 'timestamp');
		$this->hasColumn('products_featured', 'integer', 1, array(
			'type'          => 'integer',
			'length'        => 1,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		$this->hasColumn('products_last_sold', 'timestamp');
		$this->hasColumn('products_ratio', 'decimal', 5, array(
			'type'          => 'decimal',
			'length'        => 5,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0.00',
			'notnull'       => true,
			'autoincrement' => false,
			'scale'         => 2
		));

		$this->hasColumn('membership_enabled', 'string', 32, array(
			'type'    => 'string',
			'length'  => 32,
			'primary' => false,
			'notnull' => false,
			'default' => 'normal'
		));
	}

	public function getPurchaseTypes($productId, $split = false){
		$QpurchaseTypes = Doctrine_Query::create()
		->select('products_type')
		->from('Products')
		->where('products_id = ?', $productId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if ($split === true){
			$PurchaseTypes = explode(',', $QpurchaseTypes[0]['products_type']);
		}else{
			$PurchaseTypes = $QpurchaseTypes[0]['products_type'];
		}
		return $PurchaseTypes;
	}
}
?>