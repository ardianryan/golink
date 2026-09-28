<?php 
namespace Core;

use Core\Helper;
use GemError;

final class Http {
	/**
	 * URL to send request
	 * @var null
	 */
	private $_HTTPURL = NULL;
	/**
	 * CURL Response
	 * @var array
	 */
	private $_HTTPCURLRESPONSE = [];
	/**
	 * CURL Parameters
	 * @var array
	 */
	private $_HTTPCURLPARAMS = [];

	/**
	 * CURL SSL
	 *
	 * @version 6.0
	 */
	private $_HTTPCURLSSL = true;
	/**
	 * Build Http Request
	 * @version 1.0
	 * @param   string|null $url [description]
	 */
	public function __construct(?string $url = null){
		$this->_HTTPURL = $url;
		$this->_HTTPCURLPARAMS = [];
		$this->_HTTPCURLRESPONSE = [];		
		return $this;
	}
	/**
	 * Return Body
	 * @version 1.0
	 * @return  string [description]
	 */
	public function __toString(){
		return $this->getBody();
	}
	/**
	 * Call Statistically
	 * @version 1.0
	 * @param   string|null $url [description]
	 * @return  [type]           [description]
	 */
	public static function url(?string $url = null){
		return new self($url);
	}
	/**
	 * Get Request Body
	 * @version 1.0
	 * @return  [type] [description]
	 */
	public function getBody(){
		if(isset($this->_HTTPCURLRESPONSE["curlbody"]) && !empty($this->_HTTPCURLRESPONSE["curlbody"])) return $this->_HTTPCURLRESPONSE["curlbody"];
		return false;
	}
  /**
   * Get Body as Object
   * @version 1.0
   * @return  [type] [description]
   */
  	public function bodyObject(){
    	if(isset($this->_HTTPCURLRESPONSE["curlbody"]) && !empty($this->_HTTPCURLRESPONSE["curlbody"])) return json_decode($this->_HTTPCURLRESPONSE["curlbody"]);
    	return false;    
  	}
	/**
	 * Shortcut for BodyObject
	 *
	 * @version 7.5
	 * @return void
	 */
	public function json(){
		return $this->bodyObject();
	}
	/**
	 * Get HTTP Code
	 * @version 1.0
	 */
	public function httpCode($code = NULL){
		if(isset($this->_HTTPCURLRESPONSE["http_code"]) && !empty($this->_HTTPCURLRESPONSE["http_code"])) return (int) $this->_HTTPCURLRESPONSE["http_code"];
		return false;
	}	
	/**
	 * Check status code
	 *
	 * @version 7.5
	 * @return void
	 */
	public function ok(){
		if(isset($this->_HTTPCURLRESPONSE["http_code"]) && !empty($this->_HTTPCURLRESPONSE["http_code"]) && $this->_HTTPCURLRESPONSE["http_code"] >=200 && $this->_HTTPCURLRESPONSE["http_code"] < 300) return true;
		return false;
	}
  /**
   * Set Headers
   * @version 1.0
   * @param   [type] $name   [description]
   * @param   [type] $content [description]   
   */
  	public function with($name, $content){

  		$this->_HTTPCURLPARAMS["headers"][ucwords($name, "-")] = $content;
  		return $this;
  	}
  /**
   * Set headers with Array
   * @version 1.0
   * @param   array  $headers [description]
   * @return  [type]          [description]
   */
	public function withHeaders(array $headers){
		foreach ($headers as $name => $content) {
			$this->_HTTPCURLPARAMS["headers"][ucwords($name, "-")] = $content;
		}
		return $this;
	}
	/**
	 * Request Auth
	 * @version 1.0
	 * @param   [type] $username [description]
	 * @param   [type] $password [description]
	 * @return  [type]           [description]
	 */
	public function auth($username, $password){
			$this->_HTTPCURLPARAMS["auth"] = "{$username}:{$password}";
			return $this;
	}
	/**
	 * Request Body
	 * @version 1.0
	 * @param   [type] $content [description]
	 */
	public function body($content){

		if(isset($this->_HTTPCURLPARAMS["headers"]["Content-Type"]) && $this->_HTTPCURLPARAMS["headers"]["Content-Type"] == "application/json"){
			$content = json_encode($content);
		}

		$this->_HTTPCURLPARAMS["body"] = $content;
		return $this;
	}
	/**
	 * Send a get request
	 * @version 1.0
	 * @param   array  $options [description]
	 * @return  [type]          [description]
	 */
	public function get($options = []){

			if(isset($this->_HTTPCURLPARAMS["body"]) && !empty($this->_HTTPCURLPARAMS["body"])){
				
				$this->_HTTPURL .= strpos($this->_HTTPURL, "?") ? "&" : "?";

				if(is_array($this->_HTTPCURLPARAMS["body"])){
					$this->_HTTPURL .= http_build_query($this->_HTTPCURLPARAMS["body"]);
				} else{
					$this->_HTTPURL .= $this->_HTTPCURLPARAMS["body"];
				}
			}

			$curl = curl_init($this->_HTTPURL);

			if(defined('DEBUG') && DEBUG || $this->_HTTPCURLSSL == false) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			if(isset($this->_HTTPCURLPARAMS["headers"])){
				$headers = [];
				foreach ($this->_HTTPCURLPARAMS["headers"] as $name => $value) {
					if(is_array($value)) continue;
					$headers[] = "{$name}:{$value}";
				}
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			}

			if(isset($this->_HTTPCURLPARAMS["auth"]) && !empty($this->_HTTPCURLPARAMS["auth"])){
				curl_setopt($curl, CURLOPT_USERPWD, $this->_HTTPCURLPARAMS["auth"]);  
			}

			if(isset($options['timeout'])){
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']); 
				curl_setopt($curl, CURLOPT_TIMEOUT, $options['timeout']);
			}

			if(appConfig('app.proxy') && isset(appConfig('app.proxy')['enabled']) && appConfig('app.proxy')['enabled']){
				curl_setopt($curl, CURLOPT_PROXY, appConfig('app.proxy')['server'].':'.appConfig('app.proxy')['port']);
				if(!empty(appConfig('app.proxy')['user']) && !empty(appConfig('app.proxy')['password'])){
					curl_setopt($curl, CURLOPT_PROXYUSERPWD, appConfig('app.proxy')['user'].':'.appConfig('app.proxy')['password']);
				}
			}
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			
			$response = curl_exec($curl);

			$this->_HTTPCURLRESPONSE = curl_getinfo($curl);
			$this->_HTTPCURLRESPONSE["curlbody"] = $response;

			if($error = curl_error($curl)){
				GemError::log($error);
			}

			curl_close($curl);
			return $this;
	}
		/**
	 * Send a POST request
	 * @version 1.0
	 * @param   array  $options [description]
	 * @return  [type]          [description]
	 */
	public function post($options = []){    	
			$curl = curl_init($this->_HTTPURL);

			if(defined('DEBUG') && DEBUG || $this->_HTTPCURLSSL == false) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			if(isset($options["method"]) && in_array($options["method"], ["put", "patch", "delete"])){
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($options["method"]));
			}

			if(isset($this->_HTTPCURLPARAMS["headers"])){
				$headers = [];
				foreach ($this->_HTTPCURLPARAMS["headers"] as $name => $value) {
					if(is_array($value)) continue;
					$headers[] = "{$name}:{$value}";
				}
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			}

			if(isset($this->_HTTPCURLPARAMS["auth"]) && !empty($this->_HTTPCURLPARAMS["auth"])){
				curl_setopt($curl, CURLOPT_USERPWD, $this->_HTTPCURLPARAMS["auth"]);  
			}

			if(isset($options['timeout'])){
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']); 
				curl_setopt($curl, CURLOPT_TIMEOUT, $options['timeout']);
			}
				
			curl_setopt($curl, CURLOPT_POST, 1);

			if(isset($this->_HTTPCURLPARAMS["body"]) && !empty($this->_HTTPCURLPARAMS["body"])){  		
				if(is_array($this->_HTTPCURLPARAMS["body"])){
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->_HTTPCURLPARAMS["body"]));
				} else{
					curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_HTTPCURLPARAMS["body"]);
				}
			}

			if(appConfig('app.proxy') && isset(appConfig('app.proxy')['enabled']) && appConfig('app.proxy')['enabled']){
				curl_setopt($curl, CURLOPT_PROXY, appConfig('app.proxy')['server'].':'.appConfig('app.proxy')['port']);
				if(!empty(appConfig('app.proxy')['user']) && !empty(appConfig('app.proxy')['password'])){
					curl_setopt($curl, CURLOPT_PROXYUSERPWD, appConfig('app.proxy')['user'].':'.appConfig('app.proxy')['password']);
				}
			}

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			
			$response = curl_exec($curl);

			$this->_HTTPCURLRESPONSE = curl_getinfo($curl);
			$this->_HTTPCURLRESPONSE["curlbody"] = $response;

			if($error = curl_error($curl)){
				GemError::log($error);
			}

			curl_close($curl);
			return $this;
	}  
	/**
	 * Delete Request
	 * @version 1.0
	 * @param   array  $options [description]
	 * @return  [type]          [description]
	 */
	public function delete($options = []){
		return $this->post(["method" => "delete"]);
	}  
	/**
	 * Put Request
	 * @version 1.0
	 * @param   array  $options [description]
	 * @return  [type]          [description]
	 */
		public function put($options = []){
		return $this->post(["method" => "put"]);
	}  
	/**
	 * Patch Request
	 * @version 1.0
	 * @param   array  $options [description]
	 * @return  [type]          [description]
	 */
		public function patch($options = []){
		return $this->post(["method" => "patch"]);
	}  
	/**
	 * Request Response
	 * @version 1.0
	 * @return  [type] [description]
	 */
	public function response(?string $name = null){
		if(!is_null($name)) return isset($this->_HTTPCURLRESPONSE[$name]) ? $this->_HTTPCURLRESPONSE[$name] : null;
		return $this->_HTTPCURLRESPONSE;
	}
}  