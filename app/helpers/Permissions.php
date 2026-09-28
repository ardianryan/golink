<?php
namespace Helpers;

use Core\Auth;

class Permissions {
    
    /**
     * Check if current user has permission
     *
     * @version 7.6.6
     * @param string $permission
     * @return boolean
     */
    public static function can($permission){
        
        if(!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        return $user->hasRolePermission($permission);
    }

    /**
     * Check if current user has any of the given permissions
     *
     * @version 7.6.6
     * @param array $permissions
     * @return boolean
     */
    public static function canAny($permissions){
        
        if(!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        foreach($permissions as $permission) {
            if($user->hasRolePermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if current user has all of the given permissions
     *
     * @version 7.6.6
     * @param array $permissions
     * @return boolean
     */
    public static function canAll($permissions){
        
        if(!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        foreach($permissions as $permission) {
            if(!$user->hasRolePermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get current user's role
     *
     * @version 7.6.6
     * @return Models\Role|null
     */
    public static function getRole(){
        
        if(!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        return $user->getRole();
    }

    /**
     * Get current user's role name
     *
     * @version 7.6.6
     * @return string
     */
    public static function getRoleName(){
        
        $role = self::getRole();
        return $role ? $role->name : e('No Role');
    }
} 