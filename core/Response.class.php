<?php 
namespace Core;

use Core\Helper;

final class Response {

	/**
	 * Headers variable
	 * @var array
	 */
	private $headers = [];

	/**
	 * Content Body
	 * @var null
	 */
	private $body = NULL;

	/**
	 * Response Type
	 * @var null
	 */
	private $type = NULL;

	/**
	 * Actionable content types
	 * @var array
	 */
	private $actionableTypes = ["application/json", "application/javascript"];

	/**
	 * Set Charset
	 * @var string
	 */
	private static $charset = "utf-8";

	/**
	 * Response Constructor
	 * 
	 * @version 1.0
	 * @param   mixed     $response
	 * @param   int|integer $code    
	 * @param   array       $headers 
	 */
	public function __construct($response = null, int $code = 200, array $headers = []){

		$this->setHeaders($headers)
				->setStatusCode($code)
				->setBody($response);
		return $this;
	}
	/**
	 * Call Statically
	 *
	 * @version 6.0
	 * @param [type] $response
	 * @param integer $code
	 * @param array $headers
	 * @return void
	 */
	public static function factory($response = null, int $code = 200, array $headers = []){
		return new self($response, $code, $headers);
	}
	/**
	 * Print Results
	 * @version 1.0
	 * @return  string [description]
	 */
	public function send(){

		if(is_array($this->body)){
			
			print_r($this->body);

		} elseif (is_object($this->body)){
			
			print(json_encode($this->body));

		} else {

			print($this->body);

		}	
	}
	/**
	 * Print Results but exit
	 *
	 * @version 7.2
	 * @return void
	 */
	public function exit(){

		if(is_array($this->body)){
			
			print_r($this->body);
			exit;

		} elseif (is_object($this->body)){
			
			print(json_encode($this->body));
			exit;

		} else {

			print($this->body);
			exit;

		}	
	}
	/**
	 * Set Headers 
	 *
	 * @version 1.0
	 * @param   array $headers
	 */
	private function setHeaders(array $headers){

		foreach ($headers as $name => $value) {
			
			if(empty($name) && empty($value)) continue;

			$name = Helper::clean($name);

			if(strtolower($name) == "content-type" && in_array($value, $this->actionableTypes)) $this->type = "json";						

			header("{$name}:{$value}");
		}

		return $this;
	}
	/**
	 * [json description]
	 * @version 1.0
	 * @return  [type] [description]
	 */
	public function json(){
		header("Content-Type: application/json");
		return print(json_encode($this->body));
	}

	/**
	 * Set Single Header
	 *
	 * @version 1.0
	 * @param   array $header
	 */
	public function setHeader(array $header){
		
		[$name, $value] = $header;
		
		$name = Helper::clean($name);

		header("{$name}:{$value}");

		return $this;
	}

	/**
	 * Set Charset
	 *
	 * @version 1.0
	 * @param   string $charset
	 */
	public static function setCharset(string $charset) {
    
		self::$charset = trim($charset);

	}	
	/**
	 * Set Status Code
	 *
	 * @version 1.0
	 * @param   int $code
	 */
	public function setStatusCode(int $code){

		if($code < 100 || $code > 511) $code = 200;

		http_response_code($code);

		return $this;

	}
	/**
	 * Set Response
	 *
	 * @version 1.0
	 * @param   [type] $response
	 */
	public function setBody($response){

		$this->body = $response;

		if($this->type == "json"){		
			$this->body = json_encode($this->body);
		} 

		return $this;
	}
	/**
	 * Convert to array
	 * @version 1.0
	 * @param   [type] $data [description]
	 * @return  [type]       [description]
	 */
	public static function toArray($data){
		$newData = [];
		foreach ($data as $object) {
			$newData[] = $object->asArray();
		}
		return $newData;
	}
}