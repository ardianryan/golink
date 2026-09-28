<?php
namespace Middleware;

use Core\Helper;
use Core\Request;
use Core\Localization;
use Core\Response;

final class Locale {
	/**
	 * Handle Locales
	 * @version 1.0
	 * @return  [type] [description]
	 */
	public function handle() { }
	/**
	 * Change Admin Locale
	 *
	 * @version 1.0
	 * @return void
	 */
	public function admin(){		
        Localization::setFile('admin');
        Localization::update();
	}	
}