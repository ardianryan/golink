<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Helper;
use Core\View;
use Core\Auth;

final class CheckPrivate extends Middleware {    
	/**
	 * Check Private mode
	 * @version 1.0
	 */
	public function handle(Request $request) {

        if(config('private')){

            if(Auth::logged() && Auth::user()->admin) return true;

            if($url = config('home_redir')){
                Helper::redirect()->to($url);
                exit;
            }

            View::set('title', e('Private Use'));
            View::with('private')->extend('layouts.auth'); 
            exit;
        }

        return true;        
	}
}        