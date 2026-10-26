<?php 
namespace Models;

use Gem;
use Core\Model;
use Core\Helper;

class Plans extends Model {
    /**	
	 * Table Name
	 */
	public static $_table = DBprefix.'plans';

    /**
     * Redirect if not allowed
     *
     * @version 6.0
     * @return void
     */
    public static function notAllowed(){
        
        $user = \Core\Auth::user();

        if($user->team()){
            return Helper::redirect()->to(route('dashboard'))->with('danger', e('This feature is currently unavailable. Please contact your team administrator.'));
        } 

        return Helper::redirect()->to(route('pricing'))->with('danger', e('Please choose a premium package to unlock this feature.'));
    }
    /**
     * Check limit
     *
     * @version 6.0
     * @param [type] $count
     * @param [type] $total
     * @return void
     */
    public static function checkLimit($count, $total){
        if($total > 0 && $count >= $total){
            Helper::redirect()->back()->with('danger', 'You have reach the maximum limit for this feature.');
            exit;
        }
        return false;
    }

}