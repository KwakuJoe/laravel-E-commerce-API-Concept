<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderRequest;
use App\Jobs\SendOrderCreatedMailJob;
use App\Mail\OrderCreatedMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Enum;
use Ramsey\Uuid\Uuid;
use Spatie\QueryBuilder\QueryBuilder;

class OrderController extends Controller
{
    public function index(){

        try{

            $orders = QueryBuilder::for(Order::class)
           ->allowedIncludes(['order_items', 'user']) // allowing relation
            ->with('order_items.product.images') // adding it relation as many times
            ->with('user') // adding it relation as many times
            ->allowedFilters(['order_id', 'user_id', 'order_date', 'name', 'phone', 'status'])
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at','updated_at'])
            ->paginate(100);

        // $orders = Order::with([
        //     'order_items.product.images',
        // ])->get();

            return response()->json([
                'status' => 'success',
                'messages' => 'orders queried successfully',
                'data' => $orders
            ], 200);

        }catch(\Exception $e){

            return response()->json([
                'status' => 'failed',
                'messages' =>  $e->getMessage(),
                'data' => null
            ], 500);

        }
    }

    public function createOrder(OrderRequest $request){

        try{

            // validate incoming reques
            $validated = $request->validated();

            // generate UUid for order_item
            $order_uuid = Uuid::uuid4();

            // create order first


            $order_items_array = array();

            $total_amount = 0.0;
            $order_item_total_price = 0.0;


            $order = Order::create([
                'order_id' => $order_uuid,
                'user_id' => $validated['user_id'],
                'order_date' => Carbon::now(),
                'location' => $validated['location'],
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'alternate_phone' => $validated['alternate_phone'],
                'total_amount' => $total_amount,
                'additional_information' => $validated['additional_information'],
                'status' => OrderStatus::PROCESSING,
            ]);



            foreach($validated['order_items'] as $order_item){
                // add the total price of each order item
                $order_item_total_price = $order_item['price'] * $order_item['quantity'];

                // add it to the total_amout
                $total_amount += $order_item_total_price;

                // store the order_item in db
                $new_order_item = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $order_item['product_id'],
                    'quantity' => $order_item['quantity'],
                    'price' => $order_item['price'],
                ]);

                // push order_items in the array
                array_push($order_items_array, $new_order_item);

            }

            // since the total amount is created later, then we have to update the order
            $updated_order = QueryBuilder::for(Order::class)->where('id', $order->id)
            ->where('id', $order->id)
            ->allowedIncludes(['order_items', 'user']) // allowing relation
            ->with('order_items.product.images') // adding it relation as many times
            ->with('user')
            ->first();

            $updated_order->total_amount = $total_amount;

            $updated_order->save();

            $userId = $validated['user_id'];

            // noo need for this, review later
            $user = QueryBuilder::for(User::class)->where('id', $userId)->first();


            // send email to user
            // $message = (new OrderCreatedMail($order, $user))
            //     // ->onConnection('sqs')
            //     ->onQueue('emails');

            // SendOrderCreatedMailJob::dispatch($updated_order, $user);

            dispatch(new SendOrderCreatedMailJob($updated_order, $user,));
            // $email =  new OrderCreatedMail($order, $user);

            // Mail::to($user->email)->send($email);


            return response()->json([
                'status'=> 'success',
                'message' => 'order created successfully',
                'total_amount' => $total_amount,
                'data' => [
                    'order_info' => $updated_order,
                    'order_items' => $order_items_array
                ]
            ], 200);

        }catch(\Exception $e){

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }

    }

    public function showOrder($id){

        try{

            $order = QueryBuilder::for(Order::class)->where('id', $id)
            ->where('id', $id)
            ->allowedIncludes(['order_items', 'user']) // allowing relation
            ->with('order_items.product.images') // adding it relation as many times
            ->with('user')
            ->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Ordered queries successful',
                'data' => $order
            ], 200);

        }catch(\Exception $e){

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    public function updateOrderStatus(Request $request, $order_id){

        try{

            $request->validate([
                'status' => ['required', new Enum(OrderStatus::class)],
                // 'order_status' => ['required', new EnumValue(['pending', 'processing', 'shipped', 'delivered'])],

            ]);

            $order = QueryBuilder::for(Order::class)->where('id', $order_id)->first();

            if(!$order){

                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'Order not foumd',
                    'data' => null
                ], 404);
            }

            // $order->update([
            //     'status'=> $request->status
            // ]);
            $order->status = $request->input('status');
            $order->save();

            return response()->json([
                'status'=> 'success',
                'message'=> 'Order Status updated succesfully',
                'data' => $order
            ], 200);

        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'data' => null
            ], 500);

        }
    }
}
