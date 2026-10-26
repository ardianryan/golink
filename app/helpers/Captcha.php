<?php
namespace Helpers;

use Core\Http;
use AltchaOrg\Altcha\ChallengeOptions;
use AltchaOrg\Altcha\Altcha;
use AltchaOrg\Altcha\BaseChallengeOptions;
use AltchaOrg\Altcha\Hasher\Algorithm;

final class Captcha {
    /**
     * Choose system
     *
     * @version 6.0
     */
    private $captcha = null;
    /**
     * Error
     *
     * @version 6.0
     */
    private $error = false;
    /**
     * Captcha Systems
     *
     * @version 6.6.3
     * @return void
     */
    public static function systems($system = null){

        $list = [
            1 => [
                'name' => 'reCaptcha v3',
                'display' => [Captcha::class, 'reCaptchaV3Display'],
                'validate' => [Captcha::class, 'reCaptchaValidate']
            ],       
            3 => [
                'name' => 'reCaptcha v2',
                'display' => [Captcha::class, 'reCaptchaV2Display'],
                'validate' => [Captcha::class, 'reCaptchaValidate']
            ],
            4 => [
                'name' => 'hCaptcha',
                'display' => [Captcha::class, 'hCaptchaDisplay'],
                'validate' => [Captcha::class, 'hCaptchaValidate']
            ],
            5 => [
                'name' => 'Turnstile',
                'display' => [Captcha::class, 'turnstileDisplay'],
                'validate' => [Captcha::class, 'turnstileValidate']
            ],
            6 => [
                'name' => 'Altcha',
                'display' => [Captcha::class, 'altchaDisplay'],
                'validate' => [Captcha::class, 'altchaValidate']
            ]            
        ];

		if($extended = \Core\Plugin::dispatch('captcha.extend')){
			foreach($extended as $fn){
				$list = array_merge($list, $fn);
			}
		}

		if(isset($list[$system])) return $list[$system];

		return $list;
    }
    /**
     * Select system
     *
     * @version 6.0
     */
    public function __construct(){

        $systems = self::systems();

        if(isset($systems[config('captcha')])) {
            $this->captcha = $systems[config('captcha')];
        } else {
            $this->error = true;
        }
    }
    /**
     * Display Captcha
     *
     * @version 6.0
     * @return void
     */
    public static function display($action = null){
        
        if(config('captcha') == 0) return false;
        
        $captcha = new self();

        if($captcha->error) return false;

        return \call_user_func($captcha->captcha['display'], $action);
    }
    /**
     * Validate captcha
     *
     * @version 6.0
     * @return void
     */
    public function validate($request){
        
        if(config('captcha') == 0 || $this->error) return true;

        return \call_user_func($this->captcha['validate'], $request);

    }

    /**
     * Validate Recaptcha
     *
     * @version 6.0
     * @param [type] $request
     * @return void
     */
    public function reCaptchaValidate($request){

        if(empty($request->get("g-recaptcha-response"))) throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
            
        $validation = Http::url('https://www.google.com/recaptcha/api/siteverify')->body([
            'secret' => config('captcha_private'),
            'remoteip' => $request->server('remote_addr'),
            'response' => $request->get('g-recaptcha-response')
        ])->post();

        if(!$validation->bodyObject() || $validation->bodyObject()->success === false) {
            throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
        }
        
        return true;
    }
    /**
     * Display V2 Captcha
     *
     * @version 6.0
     * @return void
     */
    public static function reCaptchaV2Display($action = null){

        return '<div class="g-recaptcha mb-3 mt-2" data-sitekey="'.config('captcha_public').'"'.(\Helpers\App::isDark() ? ' data-theme="dark"' : '').''.($action ? ' data-action="'.$action.'"' : '').'></div>
                <script src="https://www.google.com/recaptcha/api.js?hl='.\Core\Localization::locale().'" async defer></script>
                <script>
                    var recaptcha = () => {
                        return grecaptcha.reset();
                    }
                </script>';
    }
    /**
     * Display V3 Captcha
     *
     * @version 6.0
     * @return void
     */
    public static function reCaptchaV3Display($action = null){

        $rand = \Core\Helper::rand(15);
        $key = config('captcha_public');

	    return "<div data-id=\"{$rand}\" class=\"mb-3\"></div><script src=\"https://www.google.com/recaptcha/api.js?hl=".\Core\Localization::locale()."&render={$key}\"></script>
                <script>
                    var recaptcha = () => {
                        grecaptcha.execute('{$key}', {action: '".($action ?? 'login')."'}).then(function(token) {
                            $('[data-id={$rand}]').html('<input type=\"hidden\" name=\"g-recaptcha-response\" value=\"'+token+'\" />')
                        });
                    }
					grecaptcha.ready('recaptcha');
                </script>";
    }
    /**
     * Display hCaptcha
     *
     * @version 6.0
     * @return void
     */
    public static function hCaptchaDisplay($action = null){
        return '<div class="h-captcha" data-sitekey="'.config('captcha_public').'"'.(\Helpers\App::isDark() ? ' data-theme="dark"' : '').''.($action ? ' data-action="'.$action.'"' : '').'></div>
                <script src="h"https://js.hcaptcha.com/1/api.js?hl='.\Core\Localization::locale().'" async defer></script>
                <script>
                  var recaptcha = () => {
                    return hcaptcha.reset();
                  }
                </script>';
    }
    /**
     * Validate hCaptcha
     *
     * @version 6.0
     * @param [type] $request
     * @return void
     */
    public function hCaptchaValidate($request){
        
        if(empty($request->get("g-recaptcha-response"))) throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
            
        $validation = Http::url('https://hcaptcha.com/siteverify')->body([
            'secret' => config('captcha_private'),
            'response' => $request->get('g-recaptcha-response')
        ])->post();

        if(!$validation->bodyObject() || $validation->bodyObject()->success === false) {
            throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
        }       
        
        return true;
    } 
    /**
     * Display turnstile
     *
     * @version 6.6.3
     * @return void
     */
    public static function turnstileDisplay($action = null){
        return '<div class="cf-turnstile" data-sitekey="'.config('captcha_public').'"'.(\Helpers\App::isDark() ? ' data-theme="dark"' : '').''.($action ? ' data-action="'.$action.'"' : '').'></div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?hl='.\Core\Localization::locale().'" async defer></script>
                <script>
                  var recaptcha = () => {
                    return turnstile.reset();
                  }
                </script>';
    }
    /**
     * Validate turnstile
     *
     * @version 6.6.3
     * @param [type] $request
     * @return void
     */
    public function turnstileValidate($request){
        
        if(empty($request->get("cf-turnstile-response"))) throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
            
        $validation = Http::url('https://challenges.cloudflare.com/turnstile/v0/siteverify')->body([
            'secret' => config('captcha_private'),
            'response' => $request->get('cf-turnstile-response')
        ])->post();

        if(!$validation->bodyObject() || $validation->bodyObject()->success === false) {
            throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
        }       
        
        return true;
    } 
    /**
     * Display Altcha
     *
     * @version 7.0
     * @return void
     */
    public static function altchaDisplay($action = null){

        $locale = \Core\Localization::locale();

        return '<div id="altcha-container" class="my-2">
                    <altcha-widget challengeurl=\''.route('captcha.challenge').'\' style="--altcha-color-border:#dee2e6;--altcha-border-width:2px;--altcha-border-radius:5px;" hidelogo hidefooter></altcha-widget>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/altcha@latest/dist/altcha.min.js" async defer type="module"></script>
                <script>
                  var recaptcha = () => {
                    if(window.altcha && window.altcha.reset){
                        altcha.reset();
                    }
                  }
                </script>';
    }
    /**
     * Altcha Challenge
     *
     * @version 7.0
     * @return void
     */
    public static function altchaChallenge(){
        
        $altcha = new Altcha(AuthToken);

        $options = new ChallengeOptions(Algorithm::SHA256, BaseChallengeOptions::DEFAULT_MAX_NUMBER, (new \DateTimeImmutable())->add(new \DateInterval('PT10S')));

        $challenge = $altcha->createChallenge($options);
        return \Core\Response::factory($challenge)->json();
    }
    /**
     * Validate Altcha
     *
     * @version 7.0
     * @param [type] $request
     * @return void
     */
    public function altchaValidate($request){

        $response = $request->altcha;

        $altcha = new Altcha(AuthToken);
    
        if(empty($response)) {
            throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
        }

        $check = $altcha->verifySolution($response, true);

        if($check === false) {
            throw new \Exception(e("The captcha did not validate. Please try again.")."<script>recaptcha()</script>");
        }

        return true;
    }
}