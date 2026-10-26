<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Helper;
use Core\View;
use Core\Auth;

final class CheckMaintenance extends Middleware {    
	/**
	 * Check Maintenance mode
	 * @version 6.6
	 */
	public function handle(Request $request) {
        
		$user = Auth::logged() ? Auth::user() : null;
        
        if(config('maintenance') && !$user){
            View::set('title', e('Offline for Maintenance'));
            View::with('maintenance')->extend('layouts.auth'); 
            exit;
        }

        return true;        
	}
}