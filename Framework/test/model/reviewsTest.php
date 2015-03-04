<?php

class reviewsTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        reviews_model::begin();
    }

    public function tearDown() {
        reviews_model::rollback();
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
        require '/var/www/html/map/server/framework/model/reviews.model.php';
        self::$model = reviews_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        reviews_model::disconnect();
    }

    public function testAppend() {

        $this->assertTrue(false);
    }

    public function testRemove() {

        $this->assertTrue(false);
    }

    public function testGet_all_by_place_id() {

        $this->assertTrue(false);
    }

    public function testGet_all_by_author() {

        $this->assertTrue(false);
    }

    public function testMatch_author() {

        $this->assertTrue(false);
    }

    public function testReviews_history() {

        $this->assertTrue(false);
    }

    public function testStatistics() {

        $this->assertTrue(false);
    }

    public function testScaffold() {

        $this->assertTrue(false);
    }

    public function testScaffold_action() {

        $this->assertTrue(false);
    }

}