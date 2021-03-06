<?php

class AccountsReceivableSales extends Doctrine_Record
{

	public function setUp()
	{
		/*$this->hasMany('SystemStatuses', array(
			'local' => 'sale_status_id',
			'foreign' => 'status_id',
			'cascade' => array('delete')
		));*/

		$this->hasOne('Customers as Customer', array(
			'local'   => 'customers_id',
			'foreign' => 'customers_id'
		));

		$this->hasMany('AccountsReceivableSalesProducts as Products', array(
			'local'   => 'id',
			'foreign' => 'sale_id',
			'orderBy' => 'id',
			'cascade' => array('delete')
		));

		$this->hasMany('AccountsReceivableSalesTotals as Totals', array(
			'local'   => 'id',
			'foreign' => 'sale_id',
			'orderBy' => 'display_order',
			'cascade' => array('delete')
		));

		$this->hasMany('AccountsReceivableSalesTransactions as Transactions', array(
			'local'   => 'id',
			'foreign' => 'sale_id',
			'orderBy' => 'date_added',
			'cascade' => array('delete')
		));
	}

	public function preSave($event)
	{
		if (is_array($this->info_json)){
			$this->info_json = json_encode($this->info_json);
		}

		if (is_array($this->address_json)){
			$this->address_json = json_encode($this->address_json);
		}
	}

	public function preHydrate($event)
	{
		$data = $event->data;
		if (isset($data['info_json'])){
			$data['info_json'] = json_decode($data['info_json'], true);
		}
		if (isset($data['address_json'])){
			$data['address_json'] = json_decode($data['address_json'], true);
		}
		$event->data = $data;
	}

	public function setTableDefinition()
	{
		$this->setTableName('accounts_receivable_sales');

		$this->hasColumn('sale_module', 'string', 64);
		$this->hasColumn('sale_id', 'integer', 4);
		$this->hasColumn('sale_status_id', 'integer', 4);
		$this->hasColumn('sale_revision', 'integer', 4);
		$this->hasColumn('sale_most_current', 'integer', 1);
		$this->hasColumn('customers_id', 'integer', 4);
		$this->hasColumn('customers_firstname', 'string', 128);
		$this->hasColumn('customers_lastname', 'string', 128);
		$this->hasColumn('customers_email_address', 'string', 128);
		$this->hasColumn('sale_total', 'decimal', 15, array(
			'scale' => 4
		));

		$this->hasColumn('date_added', 'timestamp');
		$this->hasColumn('date_modified', 'timestamp');

		$this->hasColumn('converted_from_module', 'string', 128);
		$this->hasColumn('converted_from_id', 'integer', 4);

		$this->hasColumn('info_json', 'string', 999);
		$this->hasColumn('address_json', 'string', 999);
	}
}