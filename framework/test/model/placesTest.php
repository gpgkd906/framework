<?php

class placesTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        places_model::begin();
    }

    public function tearDown() {
        places_model::rollback();
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
        require '/var/www/html/map/server/framework/model/places.model.php';
        self::$model = places_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        places_model::disconnect();
    }

    public function testCompare_by_places() {

        $this->assertTrue(false);
    }

    public function testCreate_by_places() {

        $this->assertTrue(false);
    }

    public function testIncrease_point_by_place_id() {

        $this->assertTrue(false);
    }

    public function testDecrease_point_by_place_id() {

        $this->assertTrue(false);
    }

    public function testScaffold() {

        $this->assertTrue(false);
    }

    public function testScaffold_action() {

        $this->assertTrue(false);
    }

}