<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $owner_id = null;

        if ($user->tokenCan('owner')) {
            $owner_id = $user['id'];
        } else {
            $owner_id = $user['owner_id'];
        }

        $transactions = QueryBuilder::for(Transaction::class)
            ->allowedSorts(['created_at', 'purchase amount'])
            ->where('owner_id', $owner_id)
            ->get();

        foreach ($transactions as $item) {
               $item['product'] = $item->product;
               $item['cashier'] = $item->cashier;
               unset($item['product_id']);
               unset($item['cashier_id']);
        }

        $response = [
            'data' => $transactions
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $cashier = $request->user();

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'product_id' => 'required|numeric',
            'buyer_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $transaction = $request->only(['quantity', 'product_id', 'buyer_name']);

        // Search the product
        $product = Product::findOrFail($transaction['product_id']);

        if ($product['stock' <= 0]) {
            return response()->json([
                'message' => 'product out of stock'
            ], Response::HTTP_BAD_REQUEST);
        }
        // return  $product['stock'];
        if ($product['stock'] < $transaction['quantity']) {
            return response()->json([
                'message' => 'transaction exceeds stock'
            ], Response::HTTP_BAD_REQUEST);
        }

        // count purchase ammount
        $transaction['purchase amount'] = $transaction['quantity'] * $product['price'];

        $transaction['owner_id'] = $cashier['owner_id'];
        $transaction['cashier_id'] = $cashier['id'];

         // validate product ownership
         if ($product['owner_id'] != $transaction['owner_id']) {
            return response()->json([
                'message' => 'this product not belongs to you'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $res = Transaction::create($transaction);

            // Decreament product stock
            $product['stock'] = $product['stock'] - $transaction['quantity'];
            $product->save();

            $response = [
                'message' => 'Created',
                'data' => $res,
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => $e->errorInfo
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
        $user = $request->user();
        if ($user->tokenCan('owner')) {
            
        } else if($user->tokenCan('cashier')) {

        }

        // Join All Table
        $transaction = Transaction::findOrFail($id);
        $transaction['cashier'] = $transaction->cashier;
        $transaction['owner'] = $transaction->owner;
        $transaction['product'] = $transaction->product;

        // Remove unused field
        unset($transaction['owner_id']);
        unset($transaction['product_id']);
        unset($transaction['cashier_id']);

        $response = [
            'data' => $transaction
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $transaction = Transaction::findorFail($id);

        $user = $request->user();
        $owner_id = null;

        if ($user->tokenCan('owner')) {
            $owner_id = $user['id'];
        } else {
            $owner_id = $user['owner_id'];
        }
        if ($transaction['owner_id'] != $owner_id) {
            return abort(Response::HTTP_FORBIDDEN, 'this transaction not belongs to you');
        }

        try {
            $transaction->delete();
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
