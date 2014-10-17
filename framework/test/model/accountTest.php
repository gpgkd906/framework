<?php

class accountTest extends PHPUnit_Framework_TestCase {


    protected static $model;

    public function setUp() {
        account_model::begin();
    }

    public function tearDown() {
        account_model::rollback();
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
        require '/var/www/html/map/server/framework/model/account.model.php';
        self::$model = account_model::connect(array (
  'type' => 'mysql',
  'host' => 'localhost',
  'user' => 'root',
  'password' => '',
  'dbname' => 'mirairo_map',
  'charset' => 'utf8',
));
    }

    public static function tearDownAfterClass() {
        account_model::disconnect();
    }

    public function testApp_setup() {

        $this->assertTrue(false);
    }

    public function testFacebook_login() {

        $this->assertTrue(false);
    }

    public function testTwitter_login() {

        $this->assertTrue(false);
    }

}