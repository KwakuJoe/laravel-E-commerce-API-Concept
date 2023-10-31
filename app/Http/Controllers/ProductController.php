<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Carbon;


class ProductController extends Controller
{
    public function index(){

        try {

            // using normal query builder
            // $products = Product::orderBy("id","desc")->paginate(50);

            // using package buildder
            $products = QueryBuilder::for(Product::class)
            ->allowedIncludes(['images', 'user']) // allowing relation
            ->with('images') // adding it relation as many times
            ->with('user') // adding it relation as many times
            ->allowedFilters(['name', 'user_id', 'category_id'])
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at','updated_at'])
            ->paginate(100);

            return response()->json([
                'status' => 'success',
                'message' => 'products queried successfull',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'data' => null
            ], 200);

        }
    }


    public function createProduct(Request $request){
        try {

            // $validateData = $request->validated();
            $request->validate([
                'store_id' => 'required|exists:stores,id',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'images.*' => 'image|mimes:jpeg,png,gif|max:5120',
            ]);

            $product = Product::create([
                'name' => $request->name,
                'user_id'=> $request->user_id,
                'category_id'=> $request->category_id,
                'store_id' => $request->store_id,
                'description' => $request->description,
                'price' => $request->price,
            ]);



            if($request->hasFile('images')){
                foreach($request->file('images') as $image){

                     $name = str()->uuid(). '.' . $image->getClientOriginalExtension();

                    //  Storage::putFileAs('products', $image, $name, ['disk' => 'products', 'visibility' => 'public']);
                     Storage::putFileAs('products', $image, $name);
                    //  Storage::disk('public')->put('products', $image, $name);
                    //  $image->move(public_path('images'), $imageName);

                    ProductImages::create([
                        'product_id' => $product->id,
                        'file_path' => $name,
                    ]);

                }
            }else {

                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'Failed to upload images, please add images
                    to complete theproduct',
                    'data' => null
                ], 200);
            }

            // noew get all the images for the post created
            $images = QueryBuilder::for(ProductImages::class)->where('product_id', $product->id)->get();

            // get the user who created th product
            $user = QueryBuilder::for(User::class)->where('id', $request->user_id)->first();
            // $product->images->user;

            return response()->json([
                'status'=> 'success',
                'message'=> 'product created successfully',
                // 'alo' => $alo,
                // 'imageName' => $imageName,
                'data' => [
                    'product' => $product,
                    'product_images' => $images,
                    'user' => $user
                    ]
                ], 200);

        }catch(\Exception $e) {

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'data' => null
            ], 200);

        }
    }

    // show one
    public function showProduct($id){
        try {

            // $product = Product::where($request->id)->first();

            $product = QueryBuilder::for(Product::class)->where('id', $id)

            ->allowedIncludes(['images', 'user']) // allowing relation
            ->with('images') // adding it relation as many times
            ->with('user')
            ->first();


            if(!$product){
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'product not found 404',
                    'data'=> null
                ], 404);
            }

            return response()->json([
                'status'=> 'success',
                'message'=> 'product created successfull',
                'data'=> $product
            ], 200);

         }catch(\Exception $e) {
            return response()->json([
                'status'=> 'success',
                'message'=> $e->getMessage(),
                'data'=> null
            ], 200);
         }
    }


    // updae product
    public function updateProduct(UpdateProductRequest $request, $id){

        try{

            //  $product = Product::where('id', $id)->first();
            $product = QueryBuilder::for(Product::class)->where('id', $id)->first();

            if(!$product){
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'product not found 404',
                    'data'=> null
                ], 404);
            }

            // // update the product
            $product->update([
                'name' => $request->name,
                'user_id'=> $request->user_id,
                'category_id'=> $request->category_id,
                'store_id' => $request->store_id,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            // // save changes
            $product->save();

            return response()->json([
                'status'=> 'success',
                'message'=> 'product updated successfull',
                'data'=> $product
            ], 200);

        }catch(\Exception $e){

                return response()->json([
                    'status'=> 'failed',
                    'message'=> $e->getMessage(),
                    'data'=> null
                ], 200);

        }

    }


    public function updateProductImage(Request $request, $id, $product_id){

        try{

            // validate
            $request->validate([
                'image.' => 'image|mimes:jpeg,png,gif|max:5120',
            ]);

            // find image
            $image = QueryBuilder::for(ProductImages::class)->where('id', $id)->first();

            // check if image exits
            if(!$image){
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'image not found 404',
                    'data'=> null
                ], 404);
            }

            // if request has upload file
            if($request->hasFile('image')){
                // name the inocoming request file
                $file = $request->file('image');

               // generate name for file
                $name = str()->uuid(). '.' . $file->getClientOriginalExtension();

                // dtore file in products folder
                Storage::putFileAs('products', $file, $name, ['disk' => 'public', 'visibility' => 'public']);

                // delete the previous image from files
                Storage::disk('public')->delete('products/1acd4a5b-1f70-4c1d-b2b6-ce6732fa9c53.png');
                // Storage::delete('products'.$image->file_path);

                // update file name in ddb
                $image->product_id = $product_id;
                $image->file_path = $name;
                $image->save();




                return response()->json([
                    'status'=> 'success',
                    'message'=> 'Image updated successful',
                    'data'=> $image
                ], 200);

            }

            return $id;


        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=>$e->getMessage(),
                'data'=> null
            ], 500);

        }








        // $image->update([
        //     'name' =>
        // ]);

    }
}
