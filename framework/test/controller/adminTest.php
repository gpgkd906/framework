<?php

class adminTest extends PHPUnit_Framework_TestCase {


    protected $test_plan = array(

'index' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'index') ),

'places' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'places') ),

'reviews' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'reviews') ),
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
        require '/var/www/html/map/server/framework/controller/admin.controller.php';
    }

    public static function tearDownAfterClass() {
    
    }

    public function testIndex() {
        $this->current_plan = $this->test_plan['index'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            admin_controller::$auto_header = false;
            $controller = new admin_controller('index', 'admin', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'index');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPlaces() {
        $this->current_plan = $this->test_plan['places'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            admin_controller::$auto_header = false;
            $controller = new admin_controller('places', 'admin', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'places');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testReviews() {
        $this->current_plan = $this->test_plan['reviews'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            admin_controller::$auto_header = false;
            $controller = new admin_controller('reviews', 'admin', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'reviews');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

}