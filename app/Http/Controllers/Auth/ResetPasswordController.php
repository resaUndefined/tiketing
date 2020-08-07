<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Model\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    
    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);
        // dd(Hash::make($request->token));
        $resetPassword = Password::where([
            'email' => $request->email
        ])->first();
        if (!is_null($resetPassword)) {
            $cekToken = Hash::check($request->token, $resetPassword->token);
            if ($cekToken) {
                $user = User::where('email', $resetPassword->email)->first();
                $user->password = Hash::make($request->password);
                $user->save();
                $resetPassword->delete();

                session()->flash('message', 'Password berhasil direset');
                return redirect()->route('login');
            } else {
                session()->flash('message', 'Akun anda tidak ditemukan');
                return redirect('/');
            }
        } else {
            session()->flash('message', 'Akun anda tidak ditemukan');
            return redirect('/');
        }
        
        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["msg" => "Invalid token provided"], 400);
        }

        return response()->json(["msg" => "Password has been successfully changed"]);
    }
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'email' => ['required','email'],
    //         'token' => ['required', 'string'],
    //         'password' => ['required', 'string', 'min:6', 'confirmed']
    //     ]);
    // }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
}
