<?php
/*
	Orders Custom Fields Extension Version 1.1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_ordersCustomFields extends ExtensionBase {

	public function __construct(){
		parent::__construct('ordersCustomFields');
	}

	public function init(){
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'OrderInfoAddBlock',
			'OrderInfoAddBlockEdit',
			'OrderEditSaveTabCustomerInfo',
			'CheckoutAddBlock',
			'OrderQueryBeforeExecute',
			'CheckoutProcessPostProcess',
			'CheckoutProcessPreProcess',
			'OnepageCheckoutProcessCheckout'
		), null, $this);
	}

	public function getFields($options = array()){
		$Fields = Doctrine_Query::create()
			->from('OrdersCustomFields f')
			->leftJoin('f.Description fd')
			->where('fd.language_id = ?', Session::get('languages_id'))
			->orderBy('display_order');

		if (isset($options['type'])){
			$Fields->andWhere('f.input_type = ?', $options['type']);
		}
		return $Fields->execute();
	}

	public function getFieldHtml(OrdersCustomFields $Field, Order $Order = null){
		switch($Field->input_type){
			case 'select':
			case 'select_other':
			case 'select_address':
				$oArr = array();

				$input = htmlBase::newSelectbox()
					->setRequired($Field->input_required == 1);

				if ($Field->Options && $Field->Options->count()){
					foreach($Field->Options as $Option){
						$input->addOption(
							$Option->Description[Session::get('languages_id')]->option_name,
							$Option->Description[Session::get('languages_id')]->option_name
						);
					}
				}

				if ($Field->input_type == 'select_other'){
					$input->addOption('Other', 'Other (Fill in below)');

					$otherInput = '<div class="main" style="clear:both;margin-top:.3em;">Other: ' . htmlBase::newInput()->setName('orders_custom_field_other[' . $Field->field_id . ']')->draw() . '</div>';
				}
				break;
			case 'text':
				$input = htmlBase::newInput();
				break;
			case 'textarea':
				$input = htmlBase::newElement('textarea')->attr('rows', 3);
				break;
		}
		if (is_object($input) === false){
			echo '<pre>';print_r($Field->toArray());
		}

		$input->addClass('orderCustomField')->setName('orders_custom_field[' . $Field->field_id . ']');

		if ($Order !== null){
			$FieldValues = $Order->InfoManager->getInfo('OrdersCustomFieldsValues');
			//echo '<pre>';print_r($FieldValues);
			if (isset($FieldValues[$Field->field_id])){
				$input->val($FieldValues[$Field->field_id]['value']);
			}
		}

		return array(
			'label' => $Field->Description[Session::get('languages_id')]->field_name,
			'field' => $input->draw() . (isset($otherInput) ? $otherInput : '')
		);
	}

	public function OrderInfoAddBlock($orderId){
		$Qfields = Doctrine_Query::create()
		->from('OrdersCustomFieldsToOrders')
		->where('orders_id = ?', $orderId)
		->orderBy('field_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qfields){
			foreach($Qfields as $fInfo){
				$rows[] = '<tr>
							 <td class="main" valign="top">' . $fInfo['field_label'] . ':</td>
							 <td class="main">' . $fInfo['value'] . '</td>
							</tr>';
			}
			return '<div class="main"><b>Extra Info</b></div>' . 
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . 
				'<table cellpadding="3" cellspacing="0">' . 
					'<tr>' . 
						'<td><table cellpadding="3" cellspacing="0">' . 
							implode('', $rows) . 
						'</table></td>' . 
					'</tr>' . 
				'</table>' . 
			'</div>';
		}
	}
	
	public function OrderInfoAddBlockEdit($orderId){
		$Qfields = Doctrine_Query::create()
		->from('OrdersCustomFieldsToOrders')
		->where('orders_id = ?', $orderId)
		->orderBy('field_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qfields){
			foreach($Qfields as $fInfo){
				$rows[] = '<tr>
							 <td class="main" valign="top">' . $fInfo['field_label'] . ':</td>
							 <td class="main"><input type="text" name="custom_field[' . $fInfo['field_id'] . ']" value="' . $fInfo['value'] . '"></td>
							</tr>';
			}
			return '<div class="main"><b>Extra Info</b></div>' . 
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . 
				'<table cellpadding="3" cellspacing="0">' . 
					'<tr>' . 
						'<td><table cellpadding="3" cellspacing="0">' . 
							implode('', $rows) . 
						'</table></td>' . 
					'</tr>' . 
				'</table>' . 
			'</div>';
		}
	}
	
	public function OrderEditSaveTabCustomerInfo($orderId){
		if (isset($_POST['custom_field'])){
			foreach($_POST['custom_field'] as $fieldId => $fieldVal){
				Doctrine_Query::create()
				->update('OrdersCustomFieldsToOrders')
				->set('value', '?', $fieldVal)
				->where('orders_id = ?', $orderId)
				->andWhere('field_id = ?', $fieldId)
				->execute();
			}
		}
	}

	public function getCustomFieldByType($type){
		$Field = $this->getFields(array(
			'type' => $type
		));
		return $Field[0];
	}
	
	public function CheckoutAddBlock(){
		$Fields = $this->getFields();
		if ($Qfields->count() > 0){
			foreach($Fields as $Field){
				$fieldId = $Field->field_id;
				$fieldType = $Field->input_type;
				$fieldName = $Field->Description[Session::get('languages_id')]->field_name;
				$fieldRequired = ($Field->input_required == 1);
			
				$input = '';
				switch($fieldType){
					case 'select':
					case 'select_other':
						$oArr = array();
					
						$input = htmlBase::newElement('selectbox');

						if ($Field->Options && $Field->Options->count()){
							foreach($Field->Options as $Option){
								$input->addOption(
									$Option->Description[Session::get('languages_id')]->option_name,
									$Option->Description[Session::get('languages_id')]->option_name
								);
							}
						}
						
						if ($fieldType == 'select_other'){
							$input->addOption('Other', 'Other (Fill in below)');
						
							$otherInput = '<div class="main" style="clear:both;margin-top:.3em;">Other: ' . tep_draw_input_field('orders_custom_field_other[' . $fieldId . ']') . '</div>';
						}
						break;
					case 'text':
						$input = htmlBase::newElement('input');
						break;
					case 'textarea':
						$input = htmlBase::newElement('textarea')->attr('rows', 3)->attr('cols', 30);
						break;
				}
				$input->setName('orders_custom_field[' . $fieldId . ']');
				if ($fieldRequired === true){
					$input->css('float', 'left');
				}
				
				$rows[] = '<tr>
							 <td class="main" valign="top">' . $fieldName . ':</td>
							 <td class="main">' . $input->draw() . (isset($otherInput) ? $otherInput : '') . ($fieldRequired === true ? '<div class="ui-icon ui-icon-gear required_icon" style="margin-left: 3px; margin-top: 1px; float: left;"></div>' : '') . '</td>
							</tr>';
			}
			return '<div class="main"><b>Extra Info</b></div>' . 
			'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' . 
				'<table cellpadding="3" cellspacing="0">' . 
					'<tr>' . 
						'<td><table cellpadding="3" cellspacing="0">' . 
							implode('', $rows) . 
						'</table></td>' . 
					'</tr>' . 
				'</table>' . 
			'</div>';
		}
		return '';
	}

	public function OrderQueryBeforeExecute(&$productQuery){
	}

	public function OnepageCheckoutProcessCheckout(){
		global $onePageCheckout;
		if (isset($_POST['orders_custom_field'])){
			$onePageCheckout->onePage['extra_fields']['orders_custom_field'] = $_POST['orders_custom_field'];
			if (isset($_POST['orders_custom_field_other'])){
				$onePageCheckout->onePage['extra_fields']['orders_custom_field_other'] = $_POST['orders_custom_field_other'];
			}
		}
	}
		
	public function CheckoutProcessPreProcess(&$order){
		global $messageStack, $onePageCheckout;
		if (isset($_POST['orders_custom_field'])){			
			foreach($_POST['orders_custom_field'] as $fieldId => $val){
				if (!is_array($val) && $val == ''){
					$Qcheck = Doctrine_Query::create()
					->select('input_required, input_type')
					->from('OrdersCustomFields')
					->where('field_id = ?', $fieldId)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qcheck){
						if ($Qcheck[0]['input_type'] == 'select_other' && $val == 'Other'){
							if (empty($_POST['orders_custom_field_other'][$fieldId])){
								$messageStack->addSession('pageStack', 'When selecting "Other", you must enter text in the box below', 'error');
								//tep_redirect(itw_app_link(null, 'checkout', 'default'));
							}
						}elseif ($Qcheck[0]['input_required'] == '1'){
							$messageStack->addSession('pageStack', 'A required custom field was left empty', 'error');
							//tep_redirect(itw_app_link(null, 'checkout', 'default'));
						}
					}
				}
			}
		}
	}
	
	public function CheckoutProcessPostProcess(&$order){
		global $onePageCheckout;
		if (isset($onePageCheckout->onePage['extra_fields']['orders_custom_field'])){
			foreach($onePageCheckout->onePage['extra_fields']['orders_custom_field'] as $fieldId => $val){
				if (is_array($val)) continue;
				
				$Qfield = Doctrine_Query::create()
				->select('f.field_id, f.input_type, fd.field_name')
				->from('OrdersCustomFields f')
				->leftJoin('f.OrdersCustomFieldsDescription fd')
				->where('f.field_id = ?', $fieldId)
				->andWhere('fd.language_id = ?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				
				if ($Qfield[0]['input_type'] == 'select_other' && $val == 'Other'){
					if (isset($onePageCheckout->onePage['extra_fields']['orders_custom_field_other'][$fieldId])){
						if (!empty($onePageCheckout->onePage['extra_fields']['orders_custom_field_other'][$fieldId])){
							$val = $onePageCheckout->onePage['extra_fields']['orders_custom_field_other'][$fieldId];
						}
					}
				}
				
				$field = new OrdersCustomFieldsToOrders();
				$field->value = $val;
				$field->orders_id = $order->newOrder['orderID'];
				$field->field_id = $Qfield[0]['field_id'];
				$field->field_label = $Qfield[0]['OrdersCustomFieldsDescription'][0]['field_name'];
				$field->field_type = $Qfield[0]['input_type'];
				$field->save();
			}
		}
	}
}
?>