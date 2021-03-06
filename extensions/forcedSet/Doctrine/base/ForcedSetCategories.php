<?php

/**
 * ProductsToCategories
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class ForcedSetCategories extends Doctrine_Record {

	public function setUp(){

	}

	public function setTableDefinition(){
		$this->setTableName('forced_set_categories');
		
		$this->hasColumn('forced_set_category_one_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'unique' =>true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('forced_set_category_two_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'unique' =>true,
			'autoincrement' => false,
		));
	}
}