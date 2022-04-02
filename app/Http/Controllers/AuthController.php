<?php

namespace App\Http\Controllers;

use App\Models\Cashier;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginCashier(Request $request) {
        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'required',
            'password'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cashier = Cashier::where('email', $request->input('email'))->first();

        if (!$cashier) {
            return response()->json([
                'message' => 'user with this email not found'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->input('password'), $cashier['password'])) {
            return response()->json([
                'message' => 'wrong password'
            ], Response::HTTP_UNAUTHORIZED);
        }


        $token = $cashier->createToken($request->input('email'), ['cashier'])->plainTextToken;
            return response()->json([
                'access_token' => $token
            ], Response::HTTP_OK);
    }

    public function loginOwner(Request $request) {
        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'required',
            'password'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $owner = Owner::where('email', $request->input('email'))->first();

        if (!$owner) {
            return response()->json([
                'message' => 'user with this email not found'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->input('password'), $owner['password'])) {
            return response()->json([
                'message' => 'wrong password'
            ], Response::HTTP_UNAUTHORIZED);
        }


        $token = $owner->createToken($request->input('email'), ['owner'])->plainTextToken;
            return response()->json([
                'access_token' => $token
            ], Response::HTTP_OK);
    }
}
