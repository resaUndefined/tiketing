<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Model\Role;
use Illuminate\Support\Facades\Hash;


class Helper {

    public static function get_user($user_id)
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
        $roles = Role::paginate();

        return $roles;
    }

    public static function role_nonAdmin()
    {
        $roles = Role::where('level', '!=', 0)->paginate();
        
        return $roles;
    }

    public static function get_role($role)
    {
        $role = Role::find($role);

        return $role;
    }
    
    public static function passwd($pass)
    {
        return Hash::make($pass);
    }

    public static function get_user_active($email)
    {
        $user = User::where([
            'email' => $email,
            'is_active' => 1
        ])->first();

        return $user;
    }
}