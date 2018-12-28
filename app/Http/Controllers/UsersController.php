<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
        return view('users.create');
    }

    /*
     * 用戶信息
     * */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
