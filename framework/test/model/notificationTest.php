<?php

class notificationTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        notification_model::begin();
    }

    public function tearDown() {
        notification_model::rollback();
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
        require '/var/www/html/map/server/framework/model/notification.model.php';
        self::$model = notification_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        notification_model::disconnect();
    }

    public function testBound() {

        $this->assertTrue(false);
    }

    public function testPublish() {

        $this->assertTrue(false);
    }

}