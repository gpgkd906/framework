<?php

class api2Test extends PHPUnit_Framework_TestCase {


    protected $test_plan = array(

'partners' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'partners') ),
);

    protected $current_plan = array();

    public function setUp() {
    }

    public function tearDown() {
    
    }

    public static function setUpBeforeClass() {
        if(!trait_exists('Base_core')) {
            require '/var/www/html/map/server/framework/core/Base.php';
        }
        if(!class_exists('Controller')) {
            require '/var/www/html/map/server/framework/core/Controller.php';
        }
        if(!class_exists('application')) {
            require '/var/www/html/map/server/framework/controller/application.php';
        }
        if(!class_exists('api')) {
            require '/var/www/html/map/server/framework/controller/api.php';
        }
        require '/var/www/html/map/server/framework/controller/api2.controller.php';
    }

    public static function tearDownAfterClass() {
    
    }

    public function testPartners() {
        $this->current_plan = $this->test_plan['partners'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api2_controller::$auto_header = false;
            $controller = new api2_controller('partners', 'api2', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'partners');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

}