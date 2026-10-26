<?php 
namespace Core;

use Core\Helper;
use Core\Request;

class Middleware {	
	/**
	 * Exempt List
	 *
	 * @version 6.7
	 */
	protected $_exempt = [];

	/**
	 * Check if route is allowed
	 * @version 6.7
	 * @return  [type] [description]
	 */
	protected function check(Request $request){
		foreach ($this->_exempt as $ignore) {
			if(strpos($request->path(), str_replace("*", "", $ignore)) !== false) return false;
		}

		return true;
	}
	/**
	 * Add exempts
	 *
	 * @version 6.7
	 * @param [type] $exempts
	 * @return void
	 */
	public function add($exempts){
		if(is_array($exempts)) {
			foreach($exempts as $exempt){
				if(is_array($exempt)) {
					foreach($exempt as $exempted){
						if(!in_array($exempted, $this->_exempt)) array_push($this->_exempt, $exempted);
					}
				} else {
					if(!in_array($exempt, $this->_exempt)) array_push($this->_exempt, $exempt);
				}
			}
		}
	}
}
