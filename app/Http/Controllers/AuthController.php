<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function logged_user()
{
    $user = Auth::user();

    if ($user) {
        return response()->json(['data' => $user], 200);
    } else {
        return response()->json(['message' => 'User not authenticated'], 401);
    }
}


}
