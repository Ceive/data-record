<?php


namespace Ceive\DataRecord\Tests\models;


use Ceive\DataRecord\Field\Field;
use Ceive\DataRecord\Field\FieldInteger;
use Ceive\DataRecord\Model;
use Ceive\DataRecord\Relation\RelationForeign;
use Ceive\DataRecord\Relation\RelationForeignDynamic;
use Ceive\DataRecord\Schema\Schema;

class ModelA extends Model {

    /** @var  string */
    protected $id;

    /** @var  string */
    protected $subject_schema;

    /** @var  int */
    protected $subject_id;

    /** @var  int */
    protected $user_id;

    /** @var  string */
    protected $text;

    /**
     * @return string
     */
    public function getSource(){
        return 'ex_comment';
    }

    /**
     * @param Schema $schema
     */
    public static function initialize(Schema $schema){

        $schema->setPk('id');

        $schema->setField(new FieldInteger('id'));
        $schema->setField(new FieldInteger('user_id'));
        $schema->setField(new Field('subject_schema'));
        $schema->setField(new FieldInteger('subject_id'));
        $schema->setField(new Field('text'));

    }


}