<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /*
     * 登录
     * */
    public  function create()
    {
        return view('sessions.create');
    }

    /*
     * 登录保存
     * */
    public  function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // 登录成功后的相关操作
            //Laravel 提供的 Auth::user() 方法来获取 当前登录用户 的信息，并将数据传送给路由。
            session()->flash('success', '欢迎回来！');
            //return redirect()->route('users.show', [Auth::user()]);
            //redirect() 实例提供了一个 intended 方法，该方法可将页面重定向到上一次请求尝试访问的页面上，
            //并接收一个默认跳转地址参数，当上一次请求记录为空时，跳转到默认地址上
            $fallback = route('users.show', Auth::user());
            return redirect()->intended($fallback);
        } else {
            // 登录失败后的相关操作
            //如果尝试输入错误密码则会显示登录失败的提示信息。使用 withInput() 后模板里 old('email')
            // 将能获取到上一次用户提交的内容，这样用户就无需再次输入邮箱等内容：
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
        return;
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
