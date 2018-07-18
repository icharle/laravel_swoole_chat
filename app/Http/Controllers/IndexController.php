<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\User;

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

        $OpenID = $info['id'];                  //用户唯一标识
        $NickName = $info['nickname'];          //用户用户名
        $Avatar = $info['avatar'];              //用户头像

        User::create(['NickName' => $NickName,'OpenID' =>$OpenID,'Avatar'=>$Avatar]);


    }



}
