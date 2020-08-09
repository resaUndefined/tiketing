<?php

namespace App\Http\Controllers\apk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Validator;
use App\Model\Role;

class RoleController extends ApiController
{
    public function get_all_role()
    {
        $roles = Helper::role_nonAdmin();

        $message = 'Role berhasil digenerate';
        return $this->successResponse($roles, $message, 200);
    }

    public function get_role($id)
    {
        $role = Helper::get_role($id);

        if (is_null($role)) {
            $message = 'maaf role tidak ditemukan';
            return $this->errorResponse($message, 404);
        } else {
            $message = 'Role '. $role->role. ' berhasil digenerate';
            return $this->successResponse($role, $message, 200);
        }
    }

    public function add_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'role' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $role = Role::create([
            'level' => $request->level,
            'role' => $request->role
        ]);

        if ($role) {
            $message = 'Role berhasil ditambahkan';
            return $this->successResponse($role, $message, 201);
        }else {
            $message = 'Role gagal ditambahkan';
            return $this->errorResponse($message, 500);
        }
    }

    public function update_role(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $role = Helper::get_role($id);

        if (is_null($role)) {
            $message = 'Maaf role tidak ditemukan';
            return $this->errorResponse($message, 404);
        }

        $role->level = $request->level;
        $role->role = $request->role;
        $roleSave = $role->save();

        if ($roleSave) {
            $message = 'role berhasil diubah';
            return $this->successResponse($role, $message, 200);
        } else {
            $message = 'role gagal diubah';
            return $this->errorResponse($message, 500);
        }
    }

    public function delete_role($id)
    {
        $role = Helper::get_role($id);
        
        if (is_null($role)) {
            $message = 'maaf role tidak ditemukan';
            return $this->errorResponse($message, 404);
        }
        $roleIDN = $role->role;
        $roleDelete = $role->delete();

        if ($roleDelete) {
            $message = 'role '.$roleIDN.' berhasil dihapus';
            $role = null;
            return $this->successResponse($role, $message, 200);
        } else {
            $message = 'role '.$role->role.' gagal dihapus';
            return $this->errorResponse($message, 500);
        }
        
    }
}
