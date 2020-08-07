<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->role->level == 0) {
            return redirect()->route('admin.home');
        }elseif (Auth::user()->role->level == 1) {
            return redirect()->route('vendor.home');
        }else {
            return redirect()->route('user.home');
        }
    }

    public function adminHome()
    {
        return view('admin.home');
    }

    public function vendorHome()
    {
        return view('vendor.home');
    }

    public function userHome()
    {
        return view('user.home');
    }
}
