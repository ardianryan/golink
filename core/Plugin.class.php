<?php 
namespace Core;

final class Plugin {	
	/**
	 * Plugin Directory
	 * @var array
	 */
	private static $plugins = [];   	
	
	/**
	 * Preload Plugin
	 *
	 * @version 6.0
	 * @return void
	 */
	public static function preload(){
		// Load theme config
		if($config = View::config('include')){
			foreach($config as $include){
				if(file_exists(View::$path.'/'.$include)){
					include_once View::$path.'/'.$include;
				}else{
					if(View::config('child') !== false){
						$theme = View::config('child') == true ? appConfig('app.default_theme') : View::config('child');
						include_once STORAGE.'/themes/'.$theme.'/'.$include;
					}					
				}
			}
		}
		
		// Load active plugins
		if($plugins = config('plugins')){
			foreach($plugins as $name => $plugin){
				if(file_exists(PLUGIN.'/'.$name.'/plugin.php')){
					include_once PLUGIN.'/'.$name.'/plugin.php';
				}
			}
		}
	}

	/**
	* Dispatch Event
	* @since  1.0
	* @param  string $area  Area to plugin function
	* @param  array  $param Parameters sent by the function
	*/
	public static function dispatch($area, $param = []){
		
		$return = [];      

		if(isset(self::$plugins[$area]) && is_array(self::$plugins[$area])) {
			foreach (self::$plugins[$area] as $fn) {
				if(is_array($fn) && class_exists($fn[0]) && method_exists($fn[0], $fn[1])){        
					$f = $fn[1];
					$return[] = $fn[0]::$f($param);    					   
				}elseif(is_callable($fn) || function_exists($fn)){
					$return[] = $fn($param);
				}
			}
			return $return;
		}
	}
	/**
	* Static Plug-in Function
	* @since  1.0
	* @param  string $area  Area to plugin function
	* @param  array  $param Parameters sent by the function
	*/
	public static function staticPlug($area, $param = array()){
		$return = [];      
		if(isset(self::$plugins[$area]) && is_array(self::$plugins[$area])) {
			foreach (self::$plugins[$area] as $fn) {
				$return[] = $fn;       
			}
			return $return;
		}
	}    
	/**
	* Register Event
	* @version 1.0
	* @param   [type] $area  [description]
	* @param   [type] $fn    [description]
	* @param   string $param [description]
	* @return  [type]        [description]
	*/
	public static function register($area, $fn, $param = ""){
		
		if(is_callable($fn)) {
			self::$plugins[$area][] = $fn;  
			return;
		}  

		if(is_array($fn) && class_exists($fn[0]) && method_exists($fn[0], $fn[1])){
			self::$plugins[$area][] = $fn;  
			return;    
		}

		if(is_string($fn) && function_exists($fn)) {
			self::$plugins[$area][] = $fn;  
			return;
		}

		self::$plugins[$area][] = call_user_func($fn, $param);
		return;
	}
	/**
	 * Return list of plugins
	 *
	 * @version 6.0
	 * @param [type] $area
	 * @return void
	 */
	public static function plugins($area = null){
		
		if($area && isset(self::$plugins[$area])) return self::$plugins[$area];

		return self::$plugins;
	}
	/**
	 * Extend Shortcut
	 *
	 * @version 6.6
	 * @param string $name
	 * @return void
	 */
	public static function extend(string $name, $param = []){
		return self::dispatch($name.'.extend', $param);
	}
}