<?php

/**
 * WhosOnline
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class WhosOnline extends Doctrine_Record {
	
	public function setTableDefinition(){
		$this->setTableName('whos_online');
		
		$this->hasColumn('customer_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'notnull' => false,
		'autoincrement' => false,
		));
		$this->hasColumn('full_name', 'string', 64, array(
		'type' => 'string',
		'length' => 64,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('session_id', 'string', 128, array(
		'type' => 'string',
		'length' => 128,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('ip_address', 'string', 15, array(
		'type' => 'string',
		'length' => 15,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('time_entry', 'string', 14, array(
		'type' => 'string',
		'length' => 14,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('time_last_click', 'string', 14, array(
		'type' => 'string',
		'length' => 14,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('last_page_url', 'string', 999, array(
		'type' => 'string',
		'fixed' => false,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('http_referer', 'string', 255, array(
		'type' => 'string',
		'length' => 255,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
		$this->hasColumn('user_agent', 'string', 255, array(
		'type' => 'string',
		'length' => 255,
		'fixed' => false,
		'primary' => false,
		'default' => '',
		'notnull' => true,
		'autoincrement' => false,
		));
	}
}