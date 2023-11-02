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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;




class ProductController extends Controller
{

      /**
     * Update the given blog post.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

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
                     $destination_path = 'images/products';

                    //  *** make sure you set the default  disk to public in filesystem **8 //

                    //  Storage::putFileAs('products', $image, $name, ['disk' => 'products', 'visibility' => 'public']);
                    //  Storage::putFileAs('products', $image, $name, 'public');

                    //  $contents = file_get_contents($image->getRealPath());
                    //  $name =  Storage::put('products', $contents);
                    //  $image->move(public_path('images'), $imageName);

                    // $path = Storage::put('photos', $image );
                    $path = $image->storeAs($destination_path, $name);


                    ProductImages::create([
                        'product_id' => $product->id,
                        'file_path' => $path,
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
                    'user' => $user,
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
    public function updateProduct(UpdateProductRequest $request, $id,){


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

            // check authorization
            if ($request->user()->cannot('update', $product)) {
                return 'un-authorize';
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


    public function updateProductImage(Request $request, $image_id, $product_id){

        try{

            // validate
            $request->validate([
                'image' => 'image|mimes:jpeg,png,gif|max:5120',
            ]);

            // find image
            $image = QueryBuilder::for(ProductImages::class)->where('id', $image_id)->first();

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

                $new_image = $request->file('image');

                $name = str()->uuid(). '.' . $new_image->getClientOriginalExtension();
                $destination_path = 'images/products';

                //delet old file
                $old_file_path = $image->file_path;
                Storage::delete($old_file_path);


                // store new file
                $path = $new_image->storeAs($destination_path, $name);

                // update file name in ddb
                $image->product_id = $product_id;
                $image->file_path = $path;
                $image->save();


                return response()->json([
                    'status'=> 'success',
                    'message'=> 'Image updated successful',
                    'data'=> $image
                ], 200);

            }else{

                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'Please make sure file is uploaded',
                    'data'=> null
                ], 200);

            }


        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=>$e->getMessage(),
                'data'=> null
            ], 500);

        }

    }

    public function deleteProduct($product_id){

        try{

            // find image
            $product = QueryBuilder::for(Product::class)->where('id', $product_id)->first();

            if(!$product){
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'product not found 404',
                    'data'=> null
                ], 404);
            }

            $product_images = QueryBuilder::for(ProductImages::class)->where('product_id', $product->id)->get();

            // loop image and delete them
            foreach($product_images as $image){
                // delete image in db
                $image->delete();

                // delete actual image file in server
                //reason for commeting is i have placed the deleting in the productImage model
                // its a booted function, once there is a delete on the model, it deletes the actual file too. you can handle it manually in the controller , or leave it automatically in the model

                // Storage::delete($image->file_path);

            }

            // delete the product its self
            $product->delete();

            return response()->json([
                'status'=> 'success',
                'message'=> 'product $ images deleted successful',
                'data'=> $product
            ], 200);


        }catch(\Exception $e){
            return response()->json([
                'status'=> 'failed',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

}
