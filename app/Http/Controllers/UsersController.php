<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引入user模型
use App\Models\User;
use Mail;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            // 下面这些方法不需要登录就能访问，其他的必须登录
            'except' => ['show','create','store','index','confirmEmail']
        ]);
        //只让未登录用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
        
    }
    //显示用户列表
    public function index()
    {
        $users = User::paginate(5);
        return view('users.index',compact('users'));
    }
	//注册
    public function create()
    {
        return view('users.create');
    }

    //显示用户个人信息
    //由于 show() 方法传参时声明了类型 —— Eloquent 模型 User，对应的变量名 $user 会匹配路由片段中的 {user},这样，Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例。
    public function show(User $user)
    {
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(30);
    	// 用户对象$user等于compact('user')
    	return view('users.show',compact('user','statuses'));
    }

    //注册
    //$request 用来接收用户输入的数据
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' =>'required|confirmed|min:6'
        ]);
        //保存用户
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            ]);
        //注册后自动登录
        // Auth::login($user);
        // 激活邮箱的发送操作
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的邮箱上请注意查收！');

        // 我们可以使用 session() 方法来访问会话实例。而当我们想存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法。flash 方法接收两个参数，第一个为会话的键，第二个为会话的值，我们可以通过下面这行代码的为会话赋值。

        // session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        // 用户模型 User::create() 创建成功后会返回一个用户对象，并包含新注册用户的所有信息。我们将新注册用户的所有信息赋值给变量 $user，并通过路由跳转来进行数据绑定。
        return redirect('/');
    }

    //编辑用户
    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit', compact('user'));
    }

    //修改用户
    public function update(User $user, Request $request)
    {
        //先对用户提交的信息进行验证，最终调用 update 方法对用户对象进行更新。
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功!');

        return redirect()->route('users.show', $user->id);
    }
    //删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户');
        // 返回到上一次进行删除的操作页面
        return back();
    }
    // 发送邮件给指定用户
     protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 用户的激活操作
    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        //注册后自动登录
        Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
        return redirect()->route('users.show',[$user]);
    }
    
    
    
}