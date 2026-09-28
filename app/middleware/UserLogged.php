<?php
namespace Middleware;

use Core\Middleware;
use Core\Helper;
use Core\Auth;
use Core\Request;

final class UserLogged extends Middleware {

	/**
	 * Do not un CSRF for these routes
	 * @var array
	 */
	protected $_exempt = [];

	/**
	 * Check if user is logged in already
	 * @version 1.0
	 */
	public function handle(Request $request) {

        if(Auth::logged()) {
            Helper::redirect()->to(route('dashboard'));
			exit;
        }
		        
        return true;
		
	}
}