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

    public function waybil(Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "waybill=".$request->resi."&courier=jnt",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: e079daba710176abe3c4e8edf375cb8e"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $manifest = [];
        if ($err) {
        return "cURL Error #:" . $err;
        } else {
        $response2 = json_decode($response,true);
        $manifest = $response2["rajaongkir"]["result"]["manifest"];
        return $response2;
        $panjang = count($manifest) - 1;
        for ($i=$panjang; $i>=0 ; $i--) { 
            return $manifest[$i]["manifest_description"];
        }
        // return $response2["rajaongkir"]["result"]["manifest"];
        }
    }


    public function dapat()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        // CURLOPT_URL => "https://pro.rajaongkir.com/api/city?id&province=5", //untuk get all city by provinsi
        // CURLOPT_URL => "https://pro.rajaongkir.com/api/province?id", //untuk get all provinsi
        CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=419", //untuk get kecamatan
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key: e079daba710176abe3c4e8edf375cb8e"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $response2 =  json_decode($response,true);
            // return $response2["rajaongkir"]["results"];
            return $response2;
        }
    }
    public function ongkir(Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "origin=501&originType=city&destination=574&destinationType=subdistrict&weight=1700&courier=jne",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: e079daba710176abe3c4e8edf375cb8e"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $response2 =  json_decode($response,true);
            return $response2;
        }
    }
}
