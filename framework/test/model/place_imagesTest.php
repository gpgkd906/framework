<?php

class place_imagesTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        place_images_model::begin();
    }

    public function tearDown() {
        place_images_model::rollback();
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
        require '/var/www/html/map/server/framework/model/place_images.model.php';
        self::$model = place_images_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        place_images_model::disconnect();
    }

    public function testIncrease_vote() {

        $this->assertTrue(false);
    }

    public function testDecrease_vote() {

        $this->assertTrue(false);
    }

    public function testGet_vote() {

        $this->assertTrue(false);
    }

}