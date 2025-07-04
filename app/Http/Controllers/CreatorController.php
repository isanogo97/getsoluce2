<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enterprise;

class CreatorController extends Controller
{
    public function index()
    {
        $enterprises = Enterprise::all();
        return view('creator.index', compact('enterprises'));
    }

    public function create()
    {
        return view('creator.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Enterprise::create(['name' => $request->name]);

        return redirect()->route('creator.index')->with('success', 'Enterprise created.');
    }
}

