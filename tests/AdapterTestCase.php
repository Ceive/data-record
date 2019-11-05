<?php


namespace Ceive\DataRecord\Tests;


use Ceive\DataRecord\Schema\Schema;
use Ceive\DataRecord\SchemaManager;
use Ceive\DataRecord\Storage\Db\Driver\PDOMySQL\Driver;
use Ceive\DataRecord\Tests\models\ModelA;
use PHPUnit\Framework\TestCase;

class AdapterTestCase extends TestCase {


    public function testA() {


        $mysql = new Driver();

        $schemaManager = SchemaManager::getDefault();
        $schemaManager->storageService = function($storage){

            return $storage;

        };

        $schema = new Schema('User');


        $mA = new ModelA();

        $mA->id = 10;
        $mA->title = 'ABS';
        $mA->save();

    }

}