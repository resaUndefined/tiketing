<?php

namespace App\Http\Controllers\apk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Validator;
use App\User;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    public function get_all_user()
    {
        $users = Helper::get_all_user();
        $message = 'User berhasil didapatkan';

        return $this->successResponse($users, $message, 200);
    }

    public function add_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|integer|exists:roles,id',
            'name' => 'required|string',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|string|min:6',
            // 'is_active' => 'required|boolean'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::create([
            'role_id' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Helper::passwd($request->password),
            'is_active' => 1,
            'email_verified_at' => Helper::timestamp_sekarang()
        ]);

        if ($user) {
            $message = 'User berhasil ditambahkan';

            return $this->successResponse($user, $message, 201);
        }else {
            $message = 'User gagal ditambahkan';

            return $this->errorResponse($message, 500);
        }
    }

    public function get_user($id)
    {
        $user = DB::table('users')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('users.id',$id)
                    ->select(
                        'users.id',
                        'users.role_id',
                        'roles.level as level_role',
                        'roles.role as role',
                        'users.name',
                        'users.email',
                        'users.is_active'
                        )->first();

        if (is_null($user)) {
            $message = 'maaf user tidak ditemukan';

            return $this->errorResponse($message, 404);
        } else {
            $message = 'user berhasil dipilih';

            return $this->successResponse($user, $message, 200);
        }  
    }

    public function update_user(Request $request, $id)
    {
        $user = Helper::get_user($id);
        
        if (is_null($user)) {
            $message = 'maaf user tidak ditemukan';

            return $this->errorResponse($message, 404);
        }

        $validator = Validator::make($request->all(), [
            // 'role' => 'integer|exists:roles,id',
            'name' => 'required|string',
            'email' => 'required|email|max:100|unique:users,email,'.$user->id,
            // 'password' => 'string|min:6',
            // 'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        // $user->role_id = $request->role;
        $user->name = $request->name;
        $user->email = $request->email;
        // $user->password = Helper::passwd($request->email);
        // $user->is_active = $request->is_active;
        // $user->email_verified_at = Helper::timestamp_sekarang();
        $userUpdate = $user->save();

        if ($userUpdate) {
            $message = 'User berhasil diupdate';

            return $this->successResponse($user, $message, 200);
        } else {
            $message = 'User gagal diupdate';

            return $this->errorResponse($message, 500);
        }
    }

    public function delete_user($id)
    {
        $user = Helper::get_user($id);

        if (is_null($user)) {
            $message = 'maaf user tidak ditemukan';

            return $this->errorResponse($message, 404);
        } else {
            $dataUser = $user->name;
            $deleteUser = $user->delete();

            if ($deleteUser) {
                $message = 'user berhasil dihapus';

                return $this->successResponse($dataUser, $message, 200);
            } else {
                $message = 'user gagal dihapus';

                return $this->errorResponse($message, 500);
            }
        }
    }

    public function change_password(Request $request, $id)
    {
        $user = Helper::get_user($id);
        
        if (is_null($user)) {
            $message = 'maaf user tidak ditemukan';

            return $this->errorResponse($message, 404);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'string|min:6|confirmed'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user->password = Helper::passwd($request->password);
        $updatePass = $user->save();

        if ($updatePass) {
            $message = 'password berhasil diubah';

            return $this->successResponse($user, $message, 200);
        } else {
            $message = 'password gagal diubah';

            return $this->errorResponse($message, 500);
        }
    }

    public function change_status(Request $request, $id)
    {
        $user = Helper::get_user($id);
        
        if (is_null($user)) {
            $message = 'maaf user tidak ditemukan';

            return $this->errorResponse($message, 404);
        }

        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user->is_active = $request->is_active;
        $userUpdate = $user->save();

        if ($userUpdate) {
            
            $message = ($user->is_active) ? 'user berhasil diaktifkan' : 'user berhasil dinonaktifkan';

            return $this->successResponse($user, $message, 200);
        } else {
            $message = 'status user gagal diupdate';

            return $this->errorResponse($message, 500);
        }
        
    }
}
