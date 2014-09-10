<?php
/**
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class application extends controller {

  protected $route;
  
  public $helpers = array(

	  "view", "auth",

                          );

  protected function before_action(){}

  protected function after_action(){}

  protected function before_render(){}

  protected function after_render(){}
  
}