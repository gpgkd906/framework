<?php

class metasTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        metas_model::begin();
    }

    public function tearDown() {
        metas_model::rollback();
    }

    public static function setUpBeforeClass() {
        if(!trait_exists('Base_core')) {
            require '/var/www/html/map/server/framework/core/Base.php';
        }
        if(!class_exists('Mysql_driver')) {
            require '/var/www/html/map/server/framework/core/model_driver/Mysql.php';
        }
        if(!class_exists('Model_core')) {
            require '/var/www/html/map/server/framework/core/Model.php';
        }
        require '/var/www/html/map/server/framework/model/metas.model.php';
        self::$model = metas_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        metas_model::disconnect();
    }

    public function testRead() {

        $this->assertTrue(false);
    }

    public function testWrite() {

        $this->assertTrue(false);
    }

}