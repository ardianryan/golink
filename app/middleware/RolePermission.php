<?php
namespace Middleware;

use Core\Helper;
use Core\Auth;

class RolePermission {
    
    /**
     * Handle Role Permission Check
     *
     * @version 7.6.6
     * @param string $permission
     * @return void
     */
    public function handle($permission = null){
        
        if(!Auth::check()) {
            return Helper::redirect()->to(route('login'))->with('danger', e('Please login to continue.'));
        }

        $user = Auth::user();

        // Super admin has all permissions
        if($user->admin) {
            return true;
        }

        // Check if user has role permission
        if($permission && !$user->hasRolePermission($permission)) {
            return Helper::redirect()->to(route('admin'))->with('danger', e('You do not have permission to access this page.'));
        }

        return true;
    }
} 