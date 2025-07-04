<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Invitation;
use App\Mail\InvitationMail;

class InvitationController extends Controller
{
    public function index()
    {
        return response()->json(Invitation::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $invite = Invitation::create([
            'email' => $request->email,
            'token' => Str::uuid(),
            'enterprise_id' => $request->user()->enterprise_id,
        ]);

        Mail::to($invite->email)->send(new InvitationMail($invite));

        return back()->with('success', 'Invitation sent.');
    }
}

