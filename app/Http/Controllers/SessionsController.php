<?php

// 会话控制器，该控制器将用于处理用户登录退出相关的操作
namespace App\Http\Controllers;
use Auth;

use Illuminate\Http\Request;

class SessionsController extends Controller
{
	//显示登录页面
    public function create()
    {
    	return view('sessions.create');
    }

    public function store(Request $request)
    {
    	$credentials = $this->validate($request, [
    		'email' => 'required|email|max:255',
    		'password' =>'required'
    	]);

    	//验证用户 Auth::attempt() 方法可接收两个参数，第一个参数为需要进行用户身份认证的数组，第二个参数为是否为用户开启『记住我』功能的布尔值。
    	if (Auth::attempt($credentials,$request->has('remember'))) {
    		//登录成功后的相关操作
    		session()->flash('success', '欢迎回来！');
    		// 在 store 方法内使用了 Laravel 提供的 Auth::user() 方法来获取 当前登录用户 的信息，并将数据传送给路由。
    		return redirect()->route('users.show',[Auth::user()]);
    	} else{
    		//登录失败后的相关操作
    		//这时如果尝试输入错误密码则会显示登录失败的提示信息。使用 withInput() 后模板里 old('email') 将能获取到上一次用户提交的内容，这样用户就无需再次输入邮箱等内容：
    		session()->flash('danger','很抱歉您的邮箱和密码不匹配');
    		return redirect()->back()->withInput();
    	}
    }
    // 用户退出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出!');
        return redirect('login');
    }

}
