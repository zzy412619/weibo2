<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引入user模型
use App\Models\User;

class UsersController extends Controller
{
	//注册
    public function create()
    {
        return view('users.create');
    }

    //显示用户个人信息
    //由于 show() 方法传参时声明了类型 —— Eloquent 模型 User，对应的变量名 $user 会匹配路由片段中的 {user},这样，Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例。
    public function show(User $user)
    {
    	// 用户对象$user等于compact('user')
    	return view('users.show',compact('user'));
    }

    //注册
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' =>'required|confirmed|min:6'
        ]);
        return;
    }
    
}