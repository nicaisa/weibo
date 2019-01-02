<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /*
     *用戶主頁
     * */
    public function index(User $user)
    {
        return view('users.index', compact('user'));
    }


    /*
     * 註冊
     * */
    public function create()
    {
        //dd( trans('demo.user_not_exists'));
        return view('users.create');
    }

    /*
     * 用戶信息
     * */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /*
     * 保存用户
     * */
    public  function  store(Request $request)
    {
        //validate 方法接收两个参数，第一个参数为用户的输入数据，第二个参数为该输入数据的验证规则。
        $this->validate($request, [
            'name' => 'required|max: 50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('user.show', [$user]);
    }

    /*
     * 编辑用户资料
     * @author: plum
     * @time: 2019/1/2 16:17
     **/

    public function edit(User $user)
    {
        return view('users.edit',compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max: 50',
            'password' => 'required|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user);
    }
}
