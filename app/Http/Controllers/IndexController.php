<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Socialite;
use App\User;

class IndexController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['qq','callback']]);
    }

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

        $OpenID = $info->id;                  //用户唯一标识
        $NickName = $info->nickname;          //用户用户名
        $Avatar = $info->avatar;              //用户头像

        $user = User::updateOrCreate(
            ['OpenID' =>$OpenID],
            ['NickName' => $NickName,'Avatar'=>$Avatar]);       //入库处理

         //返回JWT token值
        if (! $token = Auth::guard('api')->fromUser($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }


}
