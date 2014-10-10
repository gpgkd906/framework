<?php

class apiTest extends PHPUnit_Framework_TestCase {


    protected $test_plan = array(

'post_place_id_search' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'post_place_id_search') ),

'place' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'place') ),

'post_place' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'post_place') ),

'post_place_image' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'post_place_image') ),

'put_place_image_vote' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'put_place_image_vote') ),

'delete_place_image_vote' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'delete_place_image_vote') ),

'post_review' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'post_review') ),

'delete_review' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'delete_review') ),

'put_review_interesting' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'put_review_interesting') ),

'delete_review_interesting' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'delete_review_interesting') ),

'place_reviews' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'place_reviews') ),

'myreview' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'myreview') ),

'myplace' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'myplace') ),

'mystatistics' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'mystatistics') ),

'profile' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'profile') ),

'put_profile' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'put_profile') ),
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
        require '/var/www/html/map/server/framework/controller/api.controller.php';
    }

    public static function tearDownAfterClass() {
    
    }

    public function testPost_place_id_search() {
        $this->current_plan = $this->test_plan['post_place_id_search'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('post_place_id_search', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'post_place_id_search');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPlace() {
        $this->current_plan = $this->test_plan['place'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('place', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'place');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPost_place() {
        $this->current_plan = $this->test_plan['post_place'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('post_place', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'post_place');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPost_place_image() {
        $this->current_plan = $this->test_plan['post_place_image'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('post_place_image', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'post_place_image');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPut_place_image_vote() {
        $this->current_plan = $this->test_plan['put_place_image_vote'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('put_place_image_vote', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'put_place_image_vote');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testDelete_place_image_vote() {
        $this->current_plan = $this->test_plan['delete_place_image_vote'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('delete_place_image_vote', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'delete_place_image_vote');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPost_review() {
        $this->current_plan = $this->test_plan['post_review'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('post_review', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'post_review');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testDelete_review() {
        $this->current_plan = $this->test_plan['delete_review'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('delete_review', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'delete_review');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPut_review_interesting() {
        $this->current_plan = $this->test_plan['put_review_interesting'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('put_review_interesting', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'put_review_interesting');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testDelete_review_interesting() {
        $this->current_plan = $this->test_plan['delete_review_interesting'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('delete_review_interesting', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'delete_review_interesting');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPlace_reviews() {
        $this->current_plan = $this->test_plan['place_reviews'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('place_reviews', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'place_reviews');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testMyreview() {
        $this->current_plan = $this->test_plan['myreview'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('myreview', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'myreview');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testMyplace() {
        $this->current_plan = $this->test_plan['myplace'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('myplace', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'myplace');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testMystatistics() {
        $this->current_plan = $this->test_plan['mystatistics'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('mystatistics', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'mystatistics');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testProfile() {
        $this->current_plan = $this->test_plan['profile'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('profile', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'profile');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testPut_profile() {
        $this->current_plan = $this->test_plan['put_profile'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            api_controller::$auto_header = false;
            $controller = new api_controller('put_profile', 'api', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'put_profile');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

}