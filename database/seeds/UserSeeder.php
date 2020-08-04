<?php

use Illuminate\Database\Seeder;
use App\Model\Role;
use App\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Helper::get_role(1);
        $user = new User();
        $user->role_id = $role->id;
        $user->name = 'admin';
        $user->email = 'admin@mail.com';
        $user->password = Hash::make('qwe123');
        $user->save();
    }
}
