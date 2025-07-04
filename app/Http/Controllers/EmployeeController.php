<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        return redirect()->route('employee.profile');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('employee.profile', compact('user'));
    }
}

