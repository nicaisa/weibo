<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        //只让登陆用户访问的方法
        //except ->把。。。排除在外
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store',  'index', 'confirmEmail']
        ]);

        //只让未登陆用户访问的方法
        //only ->只允许访问create方法
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /*
     *用戶列表的信息
     * */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
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

        //Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('user.show', [$user]);
    }

    /*
     *发送邮件给指定用户
     * */
    protected function sendEmailConfirmationTo($user)
    {
        $view ='emails.confirm';
        $data = compact('user');
        $from = '1010114387@qq.com';
        //$name = 'plum';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        /*Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->to($to)->subject($subject);
        });*/

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /*
     *邮箱激活
     * */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }


    /*
     * 编辑用户资料
     * @author: plum
     * @time: 2019/1/2 16:17
     **/

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    /*
     * 更新用户资料
     * */
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
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

    /*
     *删除用户
     * */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
