<?php
namespace Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Helper;

final class Throttle extends Middleware {

    /**
     * Bearer Token
     */
    const BEARER = 'Bearer';

    /**
     * Throttle API
     *
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function handle(Request $request){

        if(CACHE === false) return true;

        $key = str_replace('Token'.' ', '', $request->server('http_authorization'));

        $key = str_replace(self::BEARER.' ', '', $key);

        $count = Helper::cacheGet('api'.$key);

        $ratelimiter = appConfig('app.throttle');

        if($user = user()){
            if($user->has('apirate')){
                if($user->hasLimit('apirate') == '0'){
                    return;
                } else {
                    $ratelimiter = [$user->hasLimit('apirate'), 1];
                }
            }
        }

        if($count === null){
            $count = 0;
            Helper::cacheSet('api'.$key, 0,  60 * $ratelimiter[1]);
        }

        $expiry = Helper::cacheExpiry('api'.$key);

        $response = new Response();
        $response->setHeader(['X-RateLimit-Limit', $ratelimiter[0]]);
        $limit = $ratelimiter[0] - ($count+1);
        $response->setHeader(['X-RateLimit-Remaining', $limit < 0 ? 0 : $limit]);
        $response->setHeader(['X-RateLimit-Reset', $expiry->getTimestamp()]);

        if($count > 0 && $count >= $ratelimiter[0]) {
            $diff = $expiry->getTimestamp() - (new \DateTime('now'))->getTimestamp();
            $response->setBody(['error' => 429, 'message' => 'Too Many API Requests.', 'Retry-After' => $diff])->json();
            exit;
        }
        Helper::cacheUpdate('api'.$key, $count + 1);
    }
}