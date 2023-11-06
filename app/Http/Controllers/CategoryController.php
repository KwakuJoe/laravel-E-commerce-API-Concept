<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(){

        try{

            $categories = QueryBuilder::for(Category::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at','updated_at'])
            ->paginate(100);

            return response()->json([
                'status' => 'success',
                'message' => 'Categories queried successfuly',
                'data' => $categories,
            ], 200);

        }catch(\Exception $e){

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function showCategory($id){

        try{
                $category = QueryBuilder::for(Category::class)->where('id', $id)->first();

                if(!$category){
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Category not found',
                        'data' => null,
                    ], 404);

                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Category queried successfuly',
                    'data' => $category,
                ]);


            }catch(\Exception $e){

                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'data' => null,
                ], 500);
        }

    }


    public function createCategory(Request $request){

        try{

            if (!Gate::allows('delete-create-update-category')) {

                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'You are not authorize to perform t0 action',
                    'data' => null
                ], 403);

            }else{

                $request->validate([
                    'name'=> 'required|string|unique:categories,name',
                ]);

                $category = Category::create([
                    'name' => $request->name,
                ]);

                return response()->json([
                    'status'=> 'success',
                    'message' => 'Category created successfully',
                    'data'=> $category
                ], 200);
            }

        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'data'=> null,
            ], 500);
        }
    }


    public function updateCategory(Request $request, $id){

        try{

            if (!Gate::allows('delete-create-update-category')) {

                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'You are not authorize to perform t0 action',
                    'data' => null
                ], 403);

            }else{

                $request->validate([
                    'name'=> 'required|string',
                ]);

                $category = QueryBuilder::for(Category::class)->where('id', $id)->first();

                if(!$category){
                    return response()->json([
                        'status'=> 'failed',
                        'message'=> 'Category not found',
                        'data' => null
                    ], 404);
                }


                $category->name = $request->name;
                $category->save();

                return response()->json([
                    'status'=> 'success',
                    'message'=> 'Category updated successfully',
                    'data' => $category
                ], 200);

            }

        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'data' => null
            ], 500);
        }

    }

    public function deleteCategory($id){
        try{

             // auhtorisation check
            if (!Gate::allows('delete-create-update-category')) {

                 return response()->json([
                    'status'=> 'failed',
                    'message'=> 'You are not authorize to perform t0 action',
                    'data' => null
                ], 403);

            }else{


                $category = QueryBuilder::for(Category::class)->where('id', $id)->first();

                if(!$category){
                    return response()->json([
                        'status'=> 'failed',
                        'message'=> 'Category not found',
                        'data' => null
                    ], 404);
                }


                $category->delete();

                return response()->json([
                    'status'=> 'success',
                    'message'=> 'Category deleted Successful',
                    'data' => $category
                ]);

            }

        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'data' => null
            ], 500);

        }
    }

}
