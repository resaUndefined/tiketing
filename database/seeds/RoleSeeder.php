<?php

use Illuminate\Database\Seeder;
use App\Model\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levelArray = [0,1,2];
        $roleArray = ['admin', 'vendor', 'user'];
        for ($i=0; $i <3 ; $i++) { 
            $role = new Role();
            $role->level = $levelArray[$i];
            $role->role = $roleArray[$i];
            $role->save();
        }
    }
}
