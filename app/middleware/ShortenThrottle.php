<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Helper;

final class ShortenThrottle extends Middleware {

    /**
     * Rate limiter
     *
     * @version 6.4
     */
    private static $ratelimiter = [5, 1];

    /**
     * Throttle API
     *
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function handle(Request $request){

        if(CACHE === false) return true;
        
        if(!$key = $request->session('throttlekey')){
            $key = Helper::rand(12);
            $request->session('throttlekey', $key);
        }                    

        $count = Helper::cacheGet('shorten'.$key);

        if($count === null){
            $count = 0;
            Helper::cacheSet('shorten'.$key, 0,  60 * self::$ratelimiter[1]);
        }               

        $expiry = Helper::cacheExpiry('shorten'.$key);
        
        $response = new Response();
        $response->setHeader(['X-RateLimit-Limit', self::$ratelimiter[0]]);
        $response->setHeader(['X-RateLimit-Remaining', self::$ratelimiter[0] - ($count+1)]);
        $response->setHeader(['X-RateLimit-Reset', $expiry->getTimestamp()]);
        
        if($count > 0 && $count >= self::$ratelimiter[0]) {      
            $diff = $expiry->getTimestamp() - (new \DateTime('now'))->getTimestamp();
            $response->setBody(['error' => 429, 'message' => 'Too Many Requests. Please retry later.', 'Retry-After' => $diff])->json();
            exit;
        }    
        Helper::cacheUpdate('shorten'.$key, $count + 1);
    }
}