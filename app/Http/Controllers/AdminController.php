<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Enterprise;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $enterprises = Enterprise::all();

        return view('admin.index', compact('users', 'enterprises'));
    }

    public function users()
    {
        return response()->json(User::all());
    }
}

