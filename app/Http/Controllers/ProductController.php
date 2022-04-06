<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $owner = $request->user();

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters('type')
            ->defaultSort('name', '-created_at')
            ->allowedSorts('name', 'created_at')
            ->where('owner_id', $owner['id'])
            ->get();

        $response = [
            'data' => $products
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'type' => 'required|in:ticket,product',
            'stock' => 'required|numeric'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $owner = $request->user();
        $producData = $request->all();
        $producData['owner_id'] = $owner['id'];
        try {
            $product = Product::create($producData);
            $response = [
                'message' => 'Created',
                'data' => $product,
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
    public function show(Request $request ,$id)
    {
        $owner = $request->user();
        $products = DB::table('products')
            ->where('owner_id', $owner['id'])
            ->where('id', $id)
            ->first();

        if ($products == null) {
            return response()->json(['message' => 'product not found'], Response::HTTP_NOT_FOUND);
        }

        $response = [
            'data' => $products
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
        $owner = $request->user();

        $product = DB::table('products')
            ->where('id', $id)
            ->where('owner_id', $owner['id']);

        if ($product == null) {
            return response()->json(['message' => 'product not found'], Response::HTTP_NOT_FOUND);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'type' => 'required|in:ticket,product',
            'stock' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $product->update($request->all());
            $response = [
                'message' => 'updated'
            ];
            return response()->json($response, Response::HTTP_OK);

        } catch(QueryException $e) {
            return response()->json([
                'message' => $e->errorInfo
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
        $owner = $request->user();
        $product = DB::table('products')
            ->where('id', $id)
            ->where('owner_id', $owner['id'])
            ->first();

        if ($product == null) {
            return response()->json(['message' => 'product not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::table('products')
                ->where('id', $id)
                ->where('owner_id', $owner['id'])
                ->delete();
            $response = [
                'message' => 'deleted',
                'data' => $product
            ];
    
            return response()->json($response, Response::HTTP_OK);

        } catch (QueryException $e) {
            return response()->json([
                'message' => $e->errorInfo
            ]);
        }
    }
}
