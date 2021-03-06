<?php

/**
 * BlogPostToCategories
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class BlogPostToCategories extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		
		$this->hasOne('BlogPosts', array(
			'local' => 'blog_post_id',
			'foreign' => 'post_id'
		));
		
		$this->hasOne('BlogCategories', array(
			'local' => 'blog_categories_id',
			'foreign' => 'blog_categories_id'
		));
	}

	public function setUpParent(){
		$BlogCategories = Doctrine_Core::getTable('BlogCategories')->getRecordInstance();
		
		$BlogCategories->hasMany('BlogPostToCategories', array(
			'local' => 'blog_categories_id',
			'foreign' => 'blog_categories_id',
			'cascade' => array('delete')
		));
	}

	public function setTableDefinition(){
		$this->setTableName('blog_post_to_categories');
		
		$this->hasColumn('blog_post_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
		
		$this->hasColumn('blog_categories_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => false,
		));
	}
}