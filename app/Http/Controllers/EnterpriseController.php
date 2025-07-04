<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index()
    {
        return response()->json(Enterprise::all());
    }

    public function show(Enterprise $enterprise)
    {
        return response()->json($enterprise);
    }
}

