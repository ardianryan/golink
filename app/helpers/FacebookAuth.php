<?php
namespace Helpers;

use Core\View;
use Core\Response;
use Core\Request;
use Core\Helper;
use Core\Http;

final class FacebookAuth {

	private $client_id = NULL;
	private $client_secret = NULL;
	private $response = NULL;
	private $redirect_uri = NULL;
	private $auth_url = "https://www.facebook.com/v20.0/dialog/oauth?";
	private $token_url = "https://graph.facebook.com/v20.0/oauth/access_token?";
	private $info_url = "https://graph.facebook.com/";

    /**
     * Connect OAuth
     *
     * @version 6.0
     */
    public function __construct($client_id, $client_secret, $redirect_uri){

		// Validate Code
        session_regenerate_id();

		if(empty($client_id) || empty($client_secret) || empty($redirect_uri)){
            throw new \Exception("Sorry, Facebook connect is not available right now.");			
		}

		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->redirect_uri = $redirect_uri;
    }
    /**
     * Generate Redirect URI
     *
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function redirectURI(Request $request){
        
        $state = md5(Helper::rand(32));

		$options = [
			"redirect_uri" => $this->redirect_uri,
			"state" => $state,
			"client_id" => $this->client_id,            
            "scope" => 'public_profile,email',
        ];	

        $request->session('oauth_state', $state);

        return $this->auth_url.http_build_query($options);
    }
    /**
     * Get Access Token
     *
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function getAccessToken(Request $request){

        if(!$request->state || $request->state != $request->session('oauth_state')){
            throw new \Exception("Security token doesn't match. Please try again.");
		}		

		$options = [
			"code" => $request->code,
			"client_secret" => $this->client_secret,
			"redirect_uri" => $this->redirect_uri,
			"client_id" => $this->client_id
        ];

		$response = Http::url($this->token_url)->body($options)->get();

        $response = json_decode($response);

        // Validate Response
        if(!isset($response->access_token)) { 			
            throw new \Exception('Oops. The access token is not valid. Please try again.');		 			
        }
        
        $data = Http::url($this->info_url."/me?fields=id,email,name&access_token={$response->access_token}")->get();
        
        $this->response = json_decode($data);
    }
    /**
     * Get User
     *
     * @version 6.0
     * @return void
     */
    public function getUser(){
        return $this->response;
    }
}        