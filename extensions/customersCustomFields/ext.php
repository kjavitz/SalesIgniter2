<?php
/*
	Customers Custom Fields Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_customersCustomFields extends ExtensionBase
{

	public function __construct()
	{
		parent::__construct('customersCustomFields');
	}

	public function init()
	{
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false){
			return;
		}

		EventManager::attachEvents(array(
			'CustomerSearchQueryBeforeExecute'
		), null, $this);

		if ($appExtension->isAdmin()){
			EventManager::attachEvent('BoxCustomersAddLink', null, $this);
		}
	}

	public function getGroups($filter = array())
	{
		$Qfields = Doctrine_Query::create()
			->from('CustomersCustomFieldsGroups g')
			->orderBy('g.display_order')
			->execute();
		return $Qfields;
	}

	public function getField(CustomersCustomFields $Field)
	{
		switch($Field->field_data->type){
			case 'select':
			case 'select_other':
				$input = htmlBase::newSelectbox()
					->setLabel($Field->Description[Session::get('languages_id')]->field_name)
					->setRequired($Field->field_data->required == 1)
					->allowEntry(($Field->field_data->type == 'select_other'))
					->isMultiple($Field->field_data->multiple == 1);

				if ($Field->Options && $Field->Options->count()){
					foreach($Field->Options as $Option){
						$input->addOption(
							$Option->Description[Session::get('languages_id')]->option_name,
							$Option->Description[Session::get('languages_id')]->option_name
						);
					}
				}
				break;
			case 'radio':
			case 'checkbox':
				if ($Field->field_data->type == 'radio'){
					$input = htmlBase::newRadioGroup();
				}
				else {
					$input = htmlBase::newCheckboxGroup();
				}
				$input
					->setLabel($Field->Description[Session::get('languages_id')]->field_name)
					->setChecked($Field->field_data->default_value)
					->setRequired(($Field->field_data->required == 1));

				if ($Field->Options && $Field->Options->count()){
					foreach($Field->Options as $Option){
						if ($Field->field_data->type == 'radio'){
							$Element = htmlBase::newRadio();
						}
						else {
							$Element = htmlBase::newCheckbox();
						}
						$Element
							->setLabel($Option->Description[Session::get('languages_id')]->option_name)
							->setLabelSeparator('&nbsp;')
							->setLabelPosition('right')
							->setValue($Option->Description[Session::get('languages_id')]->option_name);

						$input->addInput($Element);
					}
				}
				break;
			case 'date':
				$input = htmlBase::newDatePicker()
					->setLabel($Field->Description[Session::get('languages_id')]->field_name);
				break;
			case 'text':
				$input = htmlBase::newInput()
					->setLabel($Field->Description[Session::get('languages_id')]->field_name)
					->isMultiple($Field->field_data->multiple == 1);
				break;
			case 'textarea':
				$input = htmlBase::newElement('textarea')
					->attr('rows', 3)
					->setLabel($Field->Description[Session::get('languages_id')]->field_name);
				break;
		}
		$input
			->setLabelPosition('bottom')
			->addClass('customerCustomField')
			->setName('customers_custom_field[' . $Field->field_id . ']')
			->setRequired((bool) $Field->field_data->required);

		return $input;
	}

	public function getFieldHtml(CustomersCustomFields $Field, Order $Order = null)
	{
		$Input = $this->getField($Field);

		if ($Order !== null){
			$FieldValues = $Order->InfoManager->getInfo('CustomersCustomFieldsValues');
			//echo '<pre>';print_r($FieldValues);
			if (isset($FieldValues[$Field->field_id])){
				$Input->setValue($FieldValues[$Field->field_id]['value']);
			}
		}

		return array(
			'label' => $Field->Description[Session::get('languages_id')]->field_name,
			'field' => $Input->draw()
		);
	}

	public function BoxCustomersAddLink(&$contents)
	{
		$contents['children']['customersBox']['children'][] = array(
			'link'       => itw_app_link('appExt=customersCustomFields', 'manage', 'default', 'SSL'),
			'text'       => 'Manage Custom Fields'
		);
	}

	public function CustomerSearchQueryBeforeExecute(&$Qsearch, $searchTerm)
	{
		$Qsearch->leftJoin('c.Fields f2c')
			->leftJoin('f2c.Field');

		$Fields = Doctrine_Core::getTable('CustomersCustomFields')
			->findAll();
		foreach($Fields as $Field){
			if ($Field->field_data->use_in_search == 1){
				$Qsearch->orWhere('(f2c.field_id = ? AND f2c.value LIKE ?)', array(
					$Field->field_id,
					$searchTerm . '%'
				));
			}
		}
	}

	protected function _getFieldsQuery($settings = array())
	{
		$Query = Doctrine_Query::create()
			->select('g.group_name, f.*, fd.field_name, f2p.value, f2g.sort_order')
			->from('ProductsCustomFieldsGroups g')
			->leftJoin('g.ProductsCustomFieldsGroupsToProducts g2p')
			->leftJoin('g.ProductsCustomFieldsToGroups f2g')
			->leftJoin('f2g.ProductsCustomFields f')
			->leftJoin('f.ProductsCustomFieldsDescription fd')
			->leftJoin('f.ProductsCustomFieldsToProducts f2p')
			->where('fd.field_name is not null')
			->orderBy('g.display_order, f2g.sort_order');

		if (!empty($settings['product_id'])){
			$Query
				->andWhere('f2p.product_id = ?', (int)$settings['product_id'])
				->andWhere('g2p.product_id = ?', (int)$settings['product_id']);
		}
		else {
			$Query
				->andWhere('f.include_in_search = ?', '1')
				->addGroupBy('f2p.value');
		}

		if (!empty($settings['group_id'])){
			$Query->andWhere('g.group_id = ?', (int)$settings['group_id']);
		}

		if (isset($settings['show_on_site']) && $settings['show_on_site'] === true){
			$Query->andWhere('f.show_on_site = 1');
		}

		if (isset($settings['show_on_tab']) && $settings['show_on_tab'] === true){
			$Query->andWhere('f.show_on_tab = 1');
		}

		if (isset($settings['show_on_labels']) && $settings['show_on_labels'] === true){
			$Query->andWhere('f.show_on_labels = 1');
		}

		if (isset($settings['show_on_listing']) && $settings['show_on_listing'] === true){
			$Query->andWhere('f.show_on_listing = 1');
		}

		if (!empty($settings['language_id'])){
			$Query->andWhere('fd.language_id = ?', (int)$settings['language_id']);
		}

		return $Query;
	}

	/**
	 * @param array $data
	 * @return string|void
	 */
	public function buildFieldsetBlock(array $data)
	{
		$Groups = Doctrine_Query::create()
			->from('CustomersCustomFieldsGroups')
			->orderBy('display_order')
			->execute();
		if ($Groups){
			$GroupBlock = htmlBase::newFieldsetFormBlock()
				->setLegend('Extra Information');
			$i = 0;
			foreach($Groups as $Group){
				$BlockFields = array();
				$row = 0;
				$col = 0;
				foreach($Group->Fields as $fInfo){
					$Field = $fInfo->Field;
					$BlockFields[$row][$col] = $this->getField($Field);
					$BlockFields[$row][$col]->setLabelPosition('bottom');
					if (isset($data[$Field->field_id])){
						$BlockFields[$row][$col]->setValue($data[$Field->field_id]);
					}

					$col++;
					if ($col > 1){
						$col = 0;
						$row++;
					}
				}
				$GroupBlock->addBlock('custom_fields_' . $i, $Group->group_name, $BlockFields);
				$i++;
			}
			return $GroupBlock->draw();
		}
		return '';
	}
}

?>