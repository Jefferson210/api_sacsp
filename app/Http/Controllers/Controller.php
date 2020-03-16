<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
 //import auth facades
 use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    protected function respondWithToken($token)
    {
        Auth::factory()->setTTL(57599);
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL()
            //'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
