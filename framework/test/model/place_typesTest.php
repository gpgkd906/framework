<?php

class place_typesTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        place_types_model::begin();
    }

    public function tearDown() {
        place_types_model::rollback();
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
        require '/var/www/html/map/server/framework/model/place_types.model.php';
        self::$model = place_types_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        place_types_model::disconnect();
    }

    public function testInitialization() {

        $this->assertTrue(false);
    }

    public function testIncrease_type_by_place_id() {

        $this->assertTrue(false);
    }

    public function testDecrease_type_by_place_id() {

        $this->assertTrue(false);
    }

}