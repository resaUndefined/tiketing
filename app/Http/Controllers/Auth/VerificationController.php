<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\User;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    // public function VerifyEmail($token = null)
    // {
    // 	if($token == null) {

    // 		session()->flash('message', 'Invalid Login attempt');

    // 		return redirect()->route('login');

    // 	}

    //    $user = User::where('email_verification_token',$token)->first();

    //    if($user == null ){

    //    	session()->flash('message', 'Invalid Login attempt');

    //     return redirect()->route('login');

    //    }

    // //    $user->update([
        
    // //     'email_verified' => 1,
    // //     'email_verified_at' => Carbon::now(),
    // //     'email_verification_token' => ''

    // //    ]);
       
    // //    	session()->flash('message', 'Your account is activated, you can log in now');

    // //     return redirect()->route('login');

    // }

    public function verify($dataId, $dataHash)
    {
        $user = User::where([
            'id' => $dataId,
            'email_verified_at' => null,
            'is_active' => 0
        ])->first();
        if (is_null($user)) {
            session()->flash('message', 'Invalid login attempt');
            return redirect()->route('login');
        } else {
            $user->email_verified_at = Carbon::now()->toDateTimeString();
            $user->is_active = 1;
            $user->save();

            session()->flash('message', 'Akun anda telah aktif, kamu dapat login sekarang');
            return redirect()->route('login');
        }
        
    }
}
