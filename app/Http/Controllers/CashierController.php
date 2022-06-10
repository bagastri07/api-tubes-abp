<?php

namespace App\Http\Controllers;

use App\Models\Cashier;
use App\Models\Owner;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Response;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $owner = $request->user();
        $cashier = DB::table('cashiers')
            ->where('owner_id', $owner['id'])
            ->get();
        $response = [
            'data' => $cashier
        ];

        return response()->json($response, Response::HTTP_OK);
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
            'email' => 'required|email|unique:cashiers',
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

        $owner = $request->user();
        $request->request->add(['owner_id' => $owner['id']]);

        try {
            $cashier = Cashier::create($request->all());

            $response = [
                'message' => 'Created',
                'data' => $cashier,
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $cashier = Cashier::findOrFail($id);

        $owner = $request->user();
        if ($cashier['owner_id'] != $owner['id']) {
            return abort(Response::HTTP_FORBIDDEN, 'this cashier not belongs to you');
        }

        $response = [
            'data'=> $cashier
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function showCurrent(Request $request)
    {
        $user = $request->user();
        $cashier = Cashier::findOrFail($user['id']);

        $response = [
            'data'=> $cashier,
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
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cashier = Cashier::find($id);

        if (!$cashier) {
            return abort(Response::HTTP_NOT_FOUND, 'cashier not found');
        }

        $owner = $request->user();
        if ($cashier['owner_id'] != $owner['id']) {
            return abort(Response::HTTP_FORBIDDEN, 'this cashier not belongs to you');
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
            $cashier->update($request->except('email'));
            $response = [
                'message' => 'Updated',
                'data' => $cashier
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cashier = Cashier::find($id);

        $owner = $request->user();
        if ($cashier['owner_id'] != $owner['id']) {
            return response()->json([
                'message' => 'this cashier not belong to this owner'
            ], Response::HTTP_FORBIDDEN);
        }

        if (!Hash::check($request->input('old_password'), $cashier['password'])) {
            return response()->json([
                'message' => 'wrong password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $hashedPassword = Hash::make($request->input('new_password'));

        try {
            $cashier->password = $hashedPassword;

             $cashier->save();

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
    public function destroy($id, Request $request)
    {
        $cashier = Cashier::find($id);

        if (!$cashier) {
            abort(404, 'cashier not found');
        }

        $owner = $request->user();
        if ($cashier['owner_id'] != $owner['id']) {
            return abort(Response::HTTP_FORBIDDEN, 'this cashier not belongs to you');
        }

        try {
            $cashier->delete();
            $response = [
                'message' => 'deleted',
            ];
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }


}
