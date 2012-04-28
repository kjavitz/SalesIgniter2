<?php

/**
 * ReviewsDescription
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class ReviewsDescription extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();

		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'languages_id');

		$this->hasOne('Reviews', array(
			'local' => 'reviews_id',
			'foreign' => 'reviews_id'
		));
	}

	public function setUpParent(){
		$Reviews = Doctrine_Core::getTable('Reviews')->getRecordInstance();

		$Reviews->hasMany('ReviewsDescription', array(
			'local' => 'reviews_id',
			'foreign' => 'reviews_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('reviews_description');

		$this->hasColumn('reviews_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'autoincrement' => false,
		));
		$this->hasColumn('languages_id', 'integer', 4, array(
		'type' => 'integer',
		'length' => 4,
		'unsigned' => 0,
		'primary' => false,
		'autoincrement' => false,
		));
		$this->hasColumn('reviews_text', 'string', 999, array(
		'type' => 'string',
		'fixed' => false,
		'primary' => false,
		'notnull' => true,
		'autoincrement' => false,
		));
	}
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('ReviewsDescription')
		->where('languages_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'reviews_text' => $Record['reviews_text']
			);

			EventManager::notify('ReviewsDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);

			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);

			$newDesc = new ReviewsDescription();
			$newDesc->reviews_id = $Record['reviews_id'];
			$newDesc->languages_id = (int) $toLangId;
			$newDesc->reviews_text = $translated['reviews_text'];

			EventManager::notify('ReviewsDescriptionNewLanguageProcessBeforeSave', $newDesc);

			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('ReviewsDescription')
		->whereNotIn('languages_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('ReviewsDescription')
		->where('languages_id = ?', (int) $langId)
		->execute();
	}
}