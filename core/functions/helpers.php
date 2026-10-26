<?php
use Core\Helper;
use Core\View;

/**
  * Generate url
  * @version 1.0
  * @param   [type] $path [description]
  * @return  [type]       [description]
  */
if(!function_exists('url')){
  function url($path = NULL){
	  return Gem::$Config->url."/".$path;
  }
}
  
/**
  * Generate a link from route
  * @version 1.0
  * @param   string $name 
  * @param   string $param
  */
if(!function_exists('route')){
  function route($name, $param = NULL, $lang = NULL){
	  return Gem::href($name, $param, $lang);
  }
}
/**
  * Shorthand to Helper::CSRF()
  * @version 1.0
  */
if(!function_exists('csrf')){
  function csrf(){
	  return Helper::CSRF();
  }
}
/**
  * Shorthand to Helper::CSRF(false)
  * @version 1.0
  */
if(!function_exists('csrf_token')){
  function csrf_token(){
	  return Helper::CSRF(false);
  }
}
/**
  * Shorthand to View::meta()
  * @version 1.0
  */
if(!function_exists('meta')){
  function meta(){
	  return View::meta();
  }
}
/**
  * Shorthand to View::block()
  * @version 1.0
  * @param   string $type
  */
if(!function_exists('block')){
  function block(string $type){
	  return View::block($type);
  }
}
/**
  * Push content to blocks
  * @version 1.0
  * @param   string $content
  * @param   string $type        
  */
if(!function_exists('push')){
  function push(string $content, string $type = "style"){
	  return View::push($content, $type);
  }
}
/**
  * Shorthand to render
  * @version 1.0
  * @param   string $name
  * @param   array  $data
  */
if(!function_exists('render')){
  function render(string $name, array $data = []){
	  return View::render($name, $data);
  }
}
/**
  * DryRender
  *
  * @version 6.0
  * @param string $name
  * @param array $data
  * @return void
  */
if(!function_exists('view')){
  function view(string $name, array $data = []){
	  return View::dryRender($name, $data);
  }
}
/**
  * View Extended Content
  *
  * @version 6.0
  * @return void
  */
if(!function_exists('section')){
  function section(){
	  return View::content();
  }
}
/**
  * Return Body Class
  * @version 1.0
  * @return  [type] [description]
  */
if(!function_exists('bodyClass')){
  function bodyClass(){
	  echo View::bodyClass();
  }
}
/**
  * Assets shorthand
  * @version 1.0
  * @param   [type] $name [description]
  * @return  [type]       [description]
  */
if(!function_exists('assets')){
  function assets($name){
	  return View::assets($name);
  }
}
/**
  * Uploads shorthand
  * @version 1.0
  * @param   [type] $name [description]
  * @return  [type]       [description]
  */
if(!function_exists('uploads')){
  function uploads($name, $storage = null){
	  return View::uploads($name, $storage);
  }
}
/**
 * Redirect back
 *
 * @version 6.0
 * @return void
 */
if(!function_exists('back')){
  function back(){
	  return Helper::redirect()->back();
  }
}
/**
  * Plugin
  * @version 1.0
  * @param   [type] $name [description]
  * @return  [type]       [description]
  */
if(!function_exists('plug')){
  function plug($name, $param = []){
	  return \Core\Plugin::dispatch($name, $param);
  }
}
/**
  * Return timeago
  * @version 1.0
  * @param   [type] $time [description]
  * @return  [type]       [description]
  */
if(!function_exists('timeago')){
  function timeago($time){
	  return Helper::timeago($time);
  }
}
/**
  * Full Pagination
  * @version 1.0
  * @param   string $class [description]
  * @return  [type]        [description]
  */
if(!function_exists('pagination')){
  function pagination($class = "pagination", $liclass = 'page-item', $aclass = 'page-link'){
	  return Helper::pagination($class, $liclass, $aclass);
  }
}
/**
  * Return Simple Pagination
  * @version 1.0
  * @return  [type] [description]
  */
if(!function_exists('simplePagination')){
  function simplePagination($class = "pagination", $liclass = 'page-item', $aclass = 'page-link'){
	  return Helper::simplePagination($class, $liclass, $aclass);
  }
}
/**
  * Stop Execution
  * @version 1.0
  * @param   int    $code [description]
  * @return  [type]       [description]
  */
if(!function_exists('stop')){
  function stop(int $code, $text = "Page not found"){
	  GemError::trigger($code, $text);
	  exit;
  }
}
/**
  * Activate Middleware
  * @version 1.0
  * @param   [type] $middleware [description]
  * @return  [type]             [description]
  */
if(!function_exists('middleware')){
  function middleware($middleware){  
	  return Gem::addMiddleware($middleware);
  }
}
/**
  * Return message
  *
  * @version 6.0
  * @return void
  */
if(!function_exists('message')){
  function message(){
	  echo Helper::message();
  }
}
/**
  * Translation text
  * @version 1.0
  * @param   string $string
  */
if(!function_exists('e')){
  function e($string, $count = null, $variables = []){
	  return \Core\Localization::translate($string, $count, $variables);
  }
}
/**
 * Simplified e()
 * Does not accept $count
 *
 * @version 1.0
 * @param [type] $string
 * @param [type] $count
 * @param array $variables
 * @param string $string
 */
if(!function_exists('_e')){
  function _e($string, $variables = []){
	  return \Core\Localization::translate($string, null, $variables);
  }
}
/**
  * Echo e()
  *
  * @version 1.0
  * @param [type] $string
  * @param [type] $count
  * @param array $variables
  * @return void
  */
if(!function_exists('ee')){
  function ee($string, $count = null, $variables = []){
	  echo e($string, $count, $variables);
  }
}
/**
 * Simplified ee()
 * Does not accept $count
 *
 * @version 1.0
 * @param [type] $string
 * @param [type] $count
 * @param array $variables
 * @return void
 */
if(!function_exists('_ee')){
  function _ee($string, $variables = []){
	  echo \Core\Localization::translate($string, null, $variables);
  }
}
/**
 * Request Helper
 *
 * @version 6.0
 * @return void
 */
if(!function_exists('request')){
  function request(){
	  return new \Core\Request;
  }
}
/**
  * Get Session
  *
  * @version 1.0
  * @return void
  */
if(!function_exists('old')){
  function old($name){
	  return (new \Core\Request)->session('TEMP_'.$name);
  }
}
/**
 * Custom var Dump
 *
 * @version 6.0
 * @return void
 */
if(!function_exists('gvd')){
  function gvd(){
    var_dump(...func_get_args());
    exit;
  }
}

/**
 * IDN to UTF8
 */
if(!function_exists('idn_to_utf8')){
  function idn_to_utf8($string){
	  return $string;
  }
}
/**
 * IDN to ASCii
 */
if(!function_exists('idn_to_ascii')){
  function idn_to_ascii($string){
	  return $string;
  }
}
/**
 * Shortcut to plugin
 */
if(!function_exists('plugin')){
  function plugin($string){
  }
}
/**
 * Add to admin menu
 */
if(!function_exists('adminmenu')){
  function adminmenu(array $link){
	  return '<li class="sidebar-item"><a class="sidebar-link" href="'.$link['route'].'">'.$link['title'].'</a></li>';
  }
}
/**
 * Append Query
 *
 * @version 6.3.4
 * @param array $query
 * @return void
 */
if(!function_exists('appendquery')){
  function appendquery(array $query){
    
    $array = request()->query() ?? [];
    
    if($query) $array = array_replace($array, $query);

	  return http_build_query($array);
  }
}

function uppercountryname($name){
  $name = ucwords($name);
  return str_replace(' And ', ' and ', $name);
}