<?php

/**
 * BlogCategoriesDescription
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class BlogCategoriesDescription extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'language_id');
		
		$this->hasOne('BlogCategories', array(
			'local' => 'blog_categories_id',
			'foreign' => 'blog_categories_id'
		));
	}
	
	public function setUpParent(){
		$Categories = Doctrine_Core::getTable('BlogCategories')->getRecordInstance();
		
		$Categories->hasMany('BlogCategoriesDescription', array(
			'local' => 'blog_categories_id',
			'foreign' => 'blog_categories_id',
			'cascade' => array('delete')
		));
	}
	
	public function setTableDefinition(){
		$this->setTableName('blog_categories_description');
		
		$this->hasColumn('blog_categories_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '1',
			'autoincrement' => false
		));
		
		$this->hasColumn('blog_categories_title', 'string', 255, array(
			'type'          => 'string',
			'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'default'       => '',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('blog_categories_description_text', 'string', 999, array(
			'type'          => 'string',
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('blog_categories_htc_title', 'string', 150, array(
			'type'          => 'string',
			'length'        => 150,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('blog_categories_seo_url', 'string', 200, array(
			'type'          => 'string',
            'length'        => 200,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));

        $this->hasColumn('blog_categories_htc_desc', 'string', 255, array(
			'type'          => 'string',
            'length'        => 255,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('blog_categories_htc_keywords', 'string', 100, array(
			'type'          => 'string',
            'length'        => 100,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		$this->hasColumn('extra_fields', 'string', 999, array(
				'type'          => 'string',
				'length'        => 999,
				'fixed'         => false,
				'primary'       => false,
				'default'       => '',
				'notnull'       => true,
				'autoincrement' => false
			));

		$this->hasColumn('extra_key', 'string', 200, array(
				'type'          => 'string',
				'length'        => 200,
				'fixed'         => false,
				'primary'       => false,
				'default'       => '',
				'notnull'       => true,
				'autoincrement' => false
			));

	}
	public function newLanguageProcess($fromLangId, $toLangId){
		$Qdescription = Doctrine_Query::create()
		->from('BlogCategoriesDescription')
		->where('language_id = ?', (int) $fromLangId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qdescription as $Record){
			$toTranslate = array(
				'blog_categories_title' => $Record['blog_categories_title'],
				'blog_categories_description_text' => $Record['blog_categories_description_text'],
				'blog_categories_htc_title' => $Record['blog_categories_htc_title'],
				'blog_categories_htc_desc' => $Record['blog_categories_htc_desc'],
				'blog_categories_htc_keywords' => $Record['blog_categories_htc_keywords']
			);

			EventManager::notify('BlogCategoriesDescriptionNewLanguageProcessBeforeTranslate', $toTranslate);

			$translated = sysLanguage::translateText($toTranslate, (int) $toLangId, (int) $fromLangId);

			$newDesc = new BlogCategoriesDescription();
			$newDesc->blog_categories_id = $Record['blog_categories_id'];
			$newDesc->language_id = (int) $toLangId;
			$newDesc->blog_categories_title = $translated['blog_categories_title'];
			$newDesc->blog_categories_description_text = $translated['blog_categories_description_text'];
			$newDesc->blog_categories_htc_title = $translated['blog_categories_htc_title'];
			$newDesc->blog_categories_htc_desc = $translated['blog_categories_htc_desc'];
			$newDesc->blog_categories_htc_keywords = $translated['blog_categories_htc_keywords'];

			EventManager::notify('BlogCategoriesDescriptionNewLanguageProcessBeforeSave', $newDesc);

			$newDesc->save();
		}
	}

	public function cleanLanguageProcess($existsId){
		Doctrine_Query::create()
		->delete('BlogCategoriesDescription')
		->whereNotIn('language_id', $existsId)
		->execute();
	}

	public function deleteLanguageProcess($langId){
		Doctrine_Query::create()
		->delete('BlogCategoriesDescription')
		->where('language_id = ?', (int) $langId)
		->execute();
	}
}