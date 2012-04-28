<?php

/**
 * Categories
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class Categories extends Doctrine_Record {

	private $categoriesArr = array();

	public function setUp(){
		$this->hasMany('CategoriesDescription', array(
			'local'   => 'categories_id',
			'foreign' => 'categories_id',
			'cascade' => array('delete')
		));

		$this->hasOne('Categories as Parent', array(
			'local' => 'parent_id',
			'foreign' => 'categories_id'
		));

		$this->hasMany('Categories as Children', array(
			'local' => 'categories_id',
			'foreign' => 'parent_id',
            'orderBy' => 'sort_order',
			'cascade' => array('delete')
		));
	}
	
	public function preInsert($event){
		$this->date_added = date('Y-m-d');
	}
	
	public function preUpdate($event){
		$this->last_modified = date('Y-m-d');
	}
	
	public function setTableDefinition(){
		$this->setTableName('categories');
		
		$this->hasColumn('categories_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'autoincrement' => true
		));
		
		$this->hasColumn('categories_image', 'string', 64, array(
			'type'          => 'string',
			'length'        => 64,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('parent_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'default'       => '0',
			'notnull'       => true,
			'autoincrement' => false
		));
		
		$this->hasColumn('sort_order', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('date_added', 'timestamp', null, array(
			'type'          => 'timestamp',
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('last_modified', 'timestamp', null, array(
			'type'          => 'timestamp',
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
		
		$this->hasColumn('categories_menu', 'string', 12, array(
			'type'          => 'string',
			'length'        => 12,
			'fixed'         => false,
			'primary'       => false,
			'notnull'       => false,
			'autoincrement' => false
		));
	}
	
	public function getParentId($categoryId){
		$Query = Doctrine_Query::create()
		->select('parent_id')
		->from('Categories')
		->where('categories_id = ?', (int)$cPath_array[(sizeof($cPath_array)-1)])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Query[0]['parent_id'];
	}
	
	public function getParentSubCategories($parentId, $languageId){
		$Qcategories = Doctrine_Query::create()
		->select('c.categories_id, cd.categories_name, c.parent_id')
		->from('Categories c')
		->leftJoin('c.CategoriesDescription cd')
		->where('c.parent_id = ?', $parentId)
		->andWhere('cd.language_id = ?', $languageId)
		->orderBy('c.sort_order, cd.categories_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Qcategories;
	}

	public function recurseCategory($id, &$catArr){
		$Children = $this->getParentSubCategories($id, Session::get('languages_id'));
		if ($Children && !empty($Children)){
			foreach($Children as $cInfo){
				$catArr['children'][$cInfo['categories_id']] = array(
					'name' => addslashes($cInfo['CategoriesDescription'][0]['categories_name'])
				);
				$this->recurseCategory($cInfo['categories_id'], &$catArr['children'][$cInfo['categories_id']]);
			}
		}
	}

	public function getCategories($excluded = array()){
		$catArr = array();
		if (empty($this->categoriesArr)){
			foreach($this->getParentSubCategories(0, Session::get('languages_id')) as $category){
				$catArr[0]['children'][$category['categories_id']] = array(
					'name' => addslashes($category['CategoriesDescription'][0]['categories_name'])
				);
				$this->recurseCategory($category['categories_id'], &$catArr[0]['children'][$category['categories_id']]);
			}
			$this->categoriesArr = $catArr;
		}

		return $this->categoriesArr;
	}
}