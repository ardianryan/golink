<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Helper;

final class DemoProtect extends Middleware {
    /*
	 * Error Message
	 * @var string
	 */
	protected $message = "This feature is disabled in demo.";

	/**
	 * Validate CSRF Token
	 * @version 1.0
	 */
	public function handle(Request $request) {

        if(_STATE == 'DEMO') {
            
            if($request->isAjax()){
                (new \Core\Response(['error' => true, 'message' => e($this->message)]))->json();
                exit;
            }

            Helper::redirect()->back()->with('danger',  e($this->message));
            exit;
        }
        return true;
	}
}