<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public  function create()
    {
        return view('sessions.create');
    }

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
            return redirect()->route('users.show', [Auth::user()]);
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
