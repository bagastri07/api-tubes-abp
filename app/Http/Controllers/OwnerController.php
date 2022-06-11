<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $owner = Owner::all();
        $response = [
            'data' => $owner
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:owners',
            'password' => 'required',
            'birthday' => 'required',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hashedPassword = Hash::make($request->input('password'));
        $request->merge([
            'password' => $hashedPassword
        ]);

        try {
            $owner = Owner::create($request->all());
            $response = [
                'message' => 'Created',
                'data' => $owner,
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $owner = Owner::findOrFail($user['id']);

        $response = [
            'data'=> $owner
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $curentOwner = $request->user();

        $owner = Owner::find($curentOwner['id']);

        if (!$owner) {
            return abort(Response::HTTP_NOT_FOUND, 'owner not found');
        }

        $validator = Validator::make($request->except('email'), [
            'name' => 'required',
            'birthday' => 'required',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $owner->update($request->except('email'));
            $response = [
                'message' => 'Updated',
                'data' => $owner
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    public function updatePassword(Request $request) 
    {
        $curentOwner = $request->user();

        $owner = Owner::find($curentOwner['id']);

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!Hash::check($request->input('old_password'), $owner['password'])) {
            return response()->json([
                'message' => 'wrong password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $hashedPassword = Hash::make($request->input('new_password'));

        try {
            $owner->password = $hashedPassword;

             $owner->save();

             $response = [
                'message' => 'Password Updated'
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllShop()
    {
        $owners = Owner::all();

        $shops = [];
        
        foreach ($owners as $key=>$owner) {
            $shops[$key] = [
                'id' => $owner['id'],
                'shop' => $owner['shop'],
                'owner' => $owner['name']
            ];

            if ($owner['shop'] == null) {
                $shops[$key]['shop'] = "Toko " . $owner['name'];
            }
        }
        
        $response = [
            'data' => $shops
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}