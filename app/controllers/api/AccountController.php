<?php
namespace API;

use \Core\Helper;
use \Core\Request;
use \Core\Response;
use \Core\DB;
use \Core\Auth;
use \Models\User;

class Account {
    /**
     * Check if key has access
     *
     * @version 7.6
     */
    public function __construct(){
        $user = Auth::ApiUser();

        if(!$user->keyCan('account')) die(Response::factory(['error' => true, 'message' => 'You do not have access to this endpoint with this API key.'])->json());
    }
    /**
     * Get User
     *
     * @version 6.0
     * @return void
     */
    public function get(){
        
        $user = Auth::ApiUser();

        $response = [
            'error' => 0,
            'data' => [
                'id' => (int) $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'avatar' => $user->avatar(),
                'status' => $user->pro ? 'pro' : 'free',
                'planid' => $user->planid,
                'expires' => $user->expiration,
                'registered' => $user->date,
            ]
        ];

        return Response::factory($response)->json();
    }
    /**
     * Update User
     *
     * @version 6.0
     * @return void
     */
    public function update(Request $request){
        
        $data = $request->getJSON();

        $user = Auth::ApiUser();

        $update = false;
        
        if(isset($data->email)){

            $data->email = clean($data->email);
            
            if(!$request->validate($data->email, 'email')) return Response::factory(['error' => 1, 'message' => 'Please enter a valid email.'])->json();

            if(DB::user()->where('email', $data->email)->whereNotEqual('id', $user->id)->first()) return Response::factory(['error' => 1,  'message' => 'An account is already associated with this email.'])->json();

            $user->email = clean($data->email);

            $update = true;
        }

        if(isset($data->password)){

            $data->password = clean($data->password);
            
            if(strlen($data->password) < 5) return Response::factory(['error' => 1, 'message' => 'Password must be at least 5 characters.'])->json();
            
            Helper::set("hashCost", 8);
            $user->password = Helper::Encode($data->password);

            \Helpers\Emails::passwordChanged($user);

            $update = true;
        }

        if(!$update){
            return Response::factory(['error' => 0, 'message' => 'No changes were done to the account.'])->json();  
        }

        $user->save();
        return Response::factory(['error' => 0, 'message'=> 'Account has been successfully updated.'])->json();                
    }
}