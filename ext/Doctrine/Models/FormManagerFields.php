<?php

/**
 * FormManagerFields
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class FormManagerFields extends Doctrine_Record {

	public function setTableDefinition(){
		$this->setTableName('form_manager_fields');

		$this->hasColumn('form_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('field_module', 'string', 32, array(
			'type' => 'string',
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('field_minlength', 'integer', 2);
		$this->hasColumn('field_maxlength', 'integer', 2);
		$this->hasColumn('field_display_order', 'integer', 2);
		$this->hasColumn('field_label', 'string', 999);
		$this->hasColumn('field_tooltip', 'string', 999);
		$this->hasColumn('field_required', 'integer', 1, array('default' => '0'));
		$this->hasColumn('field_status', 'integer', 1, array('default' => '1'));
	}
}