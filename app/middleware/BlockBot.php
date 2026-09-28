<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Helper;
use Jaybizzle\CrawlerDetect\CrawlerDetect;


final class BlockBot extends Middleware {    
	/**
	 * Check Maintenance mode
	 * @version 1.0
	 */
	public function handle(Request $request) {

        $CrawlerDetect = new CrawlerDetect;

        if($CrawlerDetect->isCrawler()) {
            die();
        }

        return true;        
	}
}        