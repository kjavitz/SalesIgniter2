<?php
/*
$Id: ProductsOptionsDescription.php

I.T. Web Experts, Rental Store v2
http://www.itwebexperts.com

Copyright (c) 2009 I.T. Web Experts

This script and it's source is not redistributable
*/

class ProductsOptionsDescription extends Doctrine_Record {
	
	public function setUp(){
		$ProductsOptions = Doctrine_Core::getTable('ProductsOptions')->getRecordInstance();
		
		$ProductsOptions->hasMany('ProductsOptionsDescription', array(
			'local' => 'products_options_id',
			'foreign' => 'products_options_id',
			'cascade' => array('delete')
		));
		
		$this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'language_id');
	}
	
	public function setUpParent(){
		$ProductsOptions = Doctrine_Core::getTable('ProductsOptions')->getRecordInstance();
		
		$ProductsOptions->hasMany('ProductsOptionsDescription', array(
			'local' => 'products_options_id',
			'foreign' => 'products_options_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('products_options_description');
		
		$this->hasColumn('products_options_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'autoincrement' => false,
		));
		
		$this->hasColumn('products_options_name', 'string', 32, array(
			'type'          => 'string',
			'length'        => 32,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false,
		));
	}
	
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('ProductsOptionsDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'name' => $Record['products_options_name']
			);
			
			EventManager::notify('ProductsOptionsDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);
			
			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);
			
			$newDesc = new ProductsOptionsDescription();
			$newDesc->products_options_id = $Record['products_options_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->products_options_name = $translated['name'];
			
			EventManager::notify('ProductsOptionsDescriptionNewLanguageProcessBeforeSave', $newDesc);
			
			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('ProductsOptionsDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('ProductsOptionsDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}
?>