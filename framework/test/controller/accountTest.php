<?php

class accountTest extends PHPUnit_Framework_TestCase {


    protected $test_plan = array(

'logout' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'logout') ),

'login' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'login') ),

'facebook_login' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'facebook_login') ),

'facebook_register' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'facebook_register') ),

'api_facebook_login' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'api_facebook_login') ),

'api_facebook_logined' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'api_facebook_logined') ),

'api_twitter_login' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'api_twitter_login') ),

'api_twitter_logined' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'api_twitter_logined') ),

'twitter_login' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'twitter_login') ),

'twitter_register' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'twitter_register') ),

'add' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'add') ),

'reset_password' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'reset_password') ),

'complete' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'complete') ),

'error' => array( array('request' => array(), 'param' => array(), 'tpl_vars' => array(), 'template' => 'error') ),
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
        require '/var/www/html/map/server/framework/controller/account.controller.php';
    }

    public static function tearDownAfterClass() {
    
    }

    public function testLogout() {
        $this->current_plan = $this->test_plan['logout'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('logout', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'logout');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testLogin() {
        $this->current_plan = $this->test_plan['login'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('login', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'login');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testFacebook_login() {
        $this->current_plan = $this->test_plan['facebook_login'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('facebook_login', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'facebook_login');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testFacebook_register() {
        $this->current_plan = $this->test_plan['facebook_register'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('facebook_register', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'facebook_register');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testApi_facebook_login() {
        $this->current_plan = $this->test_plan['api_facebook_login'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('api_facebook_login', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'api_facebook_login');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testApi_facebook_logined() {
        $this->current_plan = $this->test_plan['api_facebook_logined'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('api_facebook_logined', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'api_facebook_logined');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testApi_twitter_login() {
        $this->current_plan = $this->test_plan['api_twitter_login'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('api_twitter_login', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'api_twitter_login');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testApi_twitter_logined() {
        $this->current_plan = $this->test_plan['api_twitter_logined'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('api_twitter_logined', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'api_twitter_logined');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testTwitter_login() {
        $this->current_plan = $this->test_plan['twitter_login'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('twitter_login', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'twitter_login');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testTwitter_register() {
        $this->current_plan = $this->test_plan['twitter_register'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('twitter_register', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'twitter_register');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testAdd() {
        $this->current_plan = $this->test_plan['add'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('add', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'add');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testReset_password() {
        $this->current_plan = $this->test_plan['reset_password'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('reset_password', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'reset_password');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testComplete() {
        $this->current_plan = $this->test_plan['complete'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('complete', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'complete');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

    public function testError() {
        $this->current_plan = $this->test_plan['error'];
        foreach($this->current_plan as $plan) {
            $request = $plan['request'];
            $_GET = $plan['param'];
            account_controller::$auto_header = false;
            $controller = new account_controller('error', 'account', null, $request);
            $this->assertEquals($controller->get_template(), $plan['template'] ?: 'error');
            $this->assertEquals(array_keys($controller->tpl_vars), array_keys($plan['tpl_vars']));
        }
        $this->assertTrue(false);
    }

}