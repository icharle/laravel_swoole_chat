<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

class IndexController extends Controller
{

    /**
     * QQ授权页面
     */
    public function qq()
    {
        return Socialite::with('qq')->redirect();
    }


    /**
     * QQ授权回调
     */
    public function callback()
    {
        $info = Socialite::driver('qq')->user();
        dd($info);
    }



}
