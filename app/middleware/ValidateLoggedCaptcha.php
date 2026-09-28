<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Helper;
use Helpers\Captcha;

final class ValidateLoggedCaptcha extends Middleware {

	/**
	 * Do not un Captcha for these routes
	 * @var array
	 */
	protected $_exempt = [];

	/**
	 * Validate Captcha
	 * @version 1.0
	 */
	public function handle(Request $request) {

		if(self::check($request) === false) return true;

        if(\Core\Auth::logged()) return true;

        $captcha = new Captcha;

		try{
			$captcha->validate($request);
			
			return true;

		} catch (\Exception $e){
			
			if($request->isAjax()){
				(new \Core\Response(['error' => true, 'message' => $e->getMessage(), 'token' => csrf_token()]))->json();
				exit;
			}

			Helper::redirect()->back()->with("danger", $e->getMessage());
			exit;
		}
	}
}