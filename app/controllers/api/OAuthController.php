<?php
namespace API;

use Core\Helper;
use Core\Request;
use Core\Response;
use Core\DB;
use Core\Auth;
use Core\View;
use Models\User;

class OAuth {
    /**
     * Authorization Request
     * @version 7.6
     * @return void
    */
    public function authorize(Request $request) {
        
        $error = null;

        if(!$request->clientid || !$request->redirect) {
            $error = e('Invalid request.');
        }
        
        if(!$client = DB::oauth_clients()->where('client_id', clean($request->clientid))->first()) {
            $error = e('Invalid client.');
        }

        if($client && trim($client->redirect_uri, '/') !== trim($request->redirect, '/')) {
            $error = e('Invalid redirect URL.');
        }

        View::set('title', e('Authorize Application'));

        if(!Auth::logged()) $request->session('redirect', request()->uri());

        $user = Auth::user();

        View::set('bodyClass', 'bg-primary');

        return View::with('auth.authorize', compact('client', 'error', 'user'))->extend('layouts.auth');
    }
    /**
     * Generate OAuth
     *
     * @version 7.6
     * @param \Core\Request $request
     * @return void
     */
    public function proceed(Request $request){

        if(!Auth::logged()) stop(404);

        $user = Auth::user();

        if(!$client = DB::oauth_clients()->where('client_id', clean($request->clientid))->first()) {
            return back()->with('danger', e('Invalid client.'));
        }
        
        $token = DB::oauth_access_tokens()->create();
        $token->user_id = $user->id;
        $token->client_id = $client->id;
        $token->code = md5(Helper::rand(64));
        $token->name = $client->name;
        $token->scopes = null;
        $token->created_at = Helper::dtime();
        $token->expires_at = Helper::dtime('+1 year');
        $token->save();

        return Helper::redirect()->to($client->redirect_uri.'?code='.$token->code);
    }
    /**
     * Generate an access token
     *
     * @version 7.6
     * @param \Core\Request $request
     * @return void
     */
    public function token(Request $request){

        $body = $request->getJSON();

        if(!$body) return Response::factory(['error' => true, 'message' => 'Invalid request: Missing data'])->json();

        if(!isset($body->code)) return Response::factory(['error' => true, 'message' => 'Invalid request: Missing code'])->json();

        if(!isset($body->secret)) return Response::factory(['error' => true, 'message' => 'Invalid request: Missing secret'])->json();

        if(!$token = DB::oauth_access_tokens()->where('code', clean($body->code))->first()){
            return Response::factory(['error' => true, 'message' => 'Invalid request: Invalid code'])->json();
        }

        $client = DB::oauth_clients()->where('id', $token->client_id)->first();

        if($client->client_secret !== $body->secret) return Response::factory(['error' => true, 'message' => 'Invalid request: Invalid secret'])->json();

        $token->code = md5(Helper::rand(64));
        $token->token = strtolower(Helper::rand(5)).$client->user_id.'-'.rand(10000, 99999).'-'.strtolower(Helper::rand(6)).'-'.rand(10000, 99999);
        $token->save();

        return Response::factory(['error' => false, 'access_token' => $token->token, 'expires_at' => strtotime($token->expires_at)])->json();
    }
}