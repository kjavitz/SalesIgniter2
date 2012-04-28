<?php

/**
 * QueueReorderTemp
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class QueueReorderTemp extends Doctrine_Record {
	
	public function setTableDefinition(){
		$this->setTableName('queue_reorder_temp');
		
		$this->hasColumn('customers_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
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
		$this->hasColumn('old_pty', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('new_pty', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
	}
}