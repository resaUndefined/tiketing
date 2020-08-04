<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Model\Role;

class helpers {

    public static function get_user_byId($user_id)
    {
        $user = User::find($user_id);
        
        return $user;
    }

    public static function get_all_user()
    {
        $users = User::all()->paginate();

        return $users;
    }

    public static function get_all_role()
    {
        $roles = Role::all()->paginate();

        return $roles;
    }

    public static function get_role($role)
    {
        $role = Role::find($role);

        return $role;
    }
    
}