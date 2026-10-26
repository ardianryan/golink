<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Helper;

final class CSRF extends Middleware {

	/**
	 * Do not un CSRF for these routes
	 * @var array
	 */
	protected $_exempt = ['shorten', 'user/qr/preview', 'api/*', 'admin/languages/translate', 'user/bio/'];
	
	/**
	 * Error Message
	 * @var string
	 */
	protected $message = "An unexpected error occurred. Please try again.";

	/**
	 * Validate CSRF Token
	 * @version 6.7
	 */
	public function handle(Request $request) {

		// @group Plugin
		$exempts = \Core\Plugin::dispatch('middleware.csrf.exempt');

		self::add($exempts);
		
		if(self::check($request) === false) return true;
		
		if(isset($_SESSION[Helper::CSRFNAME]) && $request->_token && ($_SESSION[Helper::CSRFNAME] == trim($request->_token))) {
			unset($_SESSION[Helper::CSRFNAME]);
			return true;
		}

		if($request->isAjax()){
			(new \Core\Response(['error' => true, 'message' => e($this->message)]))->json();
			exit;
		}

		Helper::redirect()->back()->with("danger", e($this->message));
		exit;
	}
}