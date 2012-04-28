<?php

/**
 * ManageQueue
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class ManageQueue extends Doctrine_Record {
	
	public function setTableDefinition(){
		$this->setTableName('manage_queue');
		
		$this->hasColumn('customers_queue_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => true,
		'autoincrement' => true,
		));
		$this->hasColumn('customers_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'default' => '0',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('products_id', 'string', 999, array(
		'type' => 'string',
		'fixed' => false,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('date_added', 'string', 8, array(
		'type' => 'string',
		'length' => 8,
		'fixed' => false,
		'primary' => false,
		'notnull' => false,
		'autoincrement' => false,
		));
	}
}