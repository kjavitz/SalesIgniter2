<?php

/**
 * TemplateManagerLayoutsColumnsConfiguration
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $column_id
 * @property integer $configuration_id
 * @property string $configuration_key
 * @property string $configuration_value
 * @property TemplateManagerLayoutsColumns $TemplateManagerLayoutsColumns
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PDFTemplateManagerLayoutsColumnsConfiguration extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('pdf_template_manager_layouts_columns_configuration');
        $this->hasColumn('column_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('configuration_id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('configuration_key', 'string', 128, array(
             'type' => 'string',
             'length' => '128',
             ));
        $this->hasColumn('configuration_value', 'string', 999, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->setAttribute(Doctrine_Core::ATTR_COLL_KEY, 'configuration_key');
        $this->hasOne('PDFTemplateManagerLayoutsColumns', array(
             'local' => 'column_id',
             'foreign' => 'column_id'));
    }
}