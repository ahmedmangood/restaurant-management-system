<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Table\TableResource;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;




use App\Events\OrderCreated;

use App\Events\OrderProductCanceled;


use App\Models\Order;
use App\Models\Table;

use App\Traits\ApiRespone;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;


class OrderController extends Controller
{
    use ApiRespone;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::whereNotIn('status',['served','paid'])
        ->with('products')->paginate(8);

        return OrderResource::collection($orders)
        ->additional(['message' => 'Orders Retrieved Successfully']);

    }

    /**
     * Store a newly created resource in storage.
     */



    public function store(StoreOrderRequest $request)
    {
        $data = $request->all();
        $tax = isset($data['tax']) ? $data['tax'] : 0.14;
       $service_fee = isset($data['service_fee']) ? $data['service_fee'] : 0.12;

        $total_price = $request->input('total_price') * (1 + $tax) * (1 + $service_fee) - ($data['discount'] ?? 0);
        $data['total_price'] = $total_price;

        $customer_id = $request->input('customer_id');
        $user_id = $request->input('user_id');
        // $user_id = auth()->user()->id;        
        $reservation_id = $request->input('reservation_id');

        $accepted_reservation = null;

            // $customerId = Auth::guard('customers')->id();        
        if ($customer_id&& !$user_id && $request->has('start_date') ) {
            $reservationData = [
                'start_date' => $request->input('start_date'),
                'status' => 'progress',
                'customer_id' => $customer_id,
                'table_id' => $request->input('table_id'),
                'order_id' => null,
            ];

            // dd(now());
            // dd($request->input('start_date'));
            $reservation = Reservation::create($reservationData);

            $data['reservation_id'] = $reservation->id;
            $data['table_id'] = $request->input('table_id');

            $accepted_reservation = $reservation;

        } else if ($customer_id && $user_id && $reservation_id) {
            $accepted_reservation = Reservation::where('customer_id', $customer_id)
                ->where('status', 'accepted')
                ->where('id', $reservation_id)
                ->first();

            if ($accepted_reservation) {
                $data['table_id'] = $accepted_reservation->table_id;
                $data['customer_id'] = $customer_id;
                $data['reservation_id'] = $reservation_id;
            }
        }
        if ($user_id) {
            $data['status'] = 'prepare';
        }
        $order = Order::create($data);
        if ($accepted_reservation) {
            $accepted_reservation->order_id = $order->id;
            $accepted_reservation->save();
        }

        $products = $request->input('products');
        foreach ($products as $product) {
            $extra = array_key_exists('extra', $product) ? $product['extra'] : null;

            $order->products()->attach($product['id'], [
                'quantity' => $product['quantity'],
                'total_price' => $product['total_price'],
                'status' => 'progress',
            ]);
            // dd($product['extra']);


        }
        event(new OrderCreated($order));

        return $this->success('Order added successfully', Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order=Order::with('products')->find($id);

        if(!$order){
            return $this->error('order not Exist');
        }
        return $this->sendData('',new OrderResource($order));

    }

    public function prepareOrders()
{
    $orders = Order::with('products')->where('status', 'prepare')->get();

    if($orders->isEmpty()){
        return $this->error('no prepare orders exist');
    }


    return $this->sendData('', OrderResource::collection($orders));
}


//show orders not paid to cashier

public function servedOrders()
{
    $orders = Order::with(['products','reservation'])->where('status', 'served')->get();

    if($orders->isEmpty()){
        return $this->error('no served orders exist');
    }

    return $this->sendData('', OrderResource::collection($orders));
}



    public function getTablesWithPreparedOrCompleteOrders()
    {
        $tableIds = Order::whereIn('status',['Prepare','Complete'])->pluck('table_id')->unique();

        if ($tableIds->isEmpty()) {
            return $this->error('No tables found with prepared or completed orders');
        }

        $tables = Table::whereIn('id', $tableIds)->get();

        return TableResource::collection($tables)
            ->additional(['message' => 'Tables with prepared and completed orders retrieved successfully']);
    }


    public function getOrderOrCompleteTable($table_id)
    {

        try{
        $table = Table::findOrFail($table_id);
        } catch (ModelNotFoundException $exception){
            return $this->error('Table not found', Response::HTTP_NOT_FOUND);
        }

        $prepareOrders = Order::where('table_id', '=', $table_id)->whereIn('status',['Prepare','Complete'])->with('products')->get();

        if ($prepareOrders->isEmpty()) {
            return $this->error('No prepared nor completed orders found for this table');
        }

        return OrderResource::collection($prepareOrders)
            ->additional(['message' => 'Prepared or Completed orders for table '.$table_id.' retrieved successfully']);
    }

    public function getOrderServedByTable($table_id)
    {

        try{
        $table = Table::findOrFail($table_id);
        } catch (ModelNotFoundException $exception){
            return $this->error('Table not found', Response::HTTP_NOT_FOUND);
        }

        $prepareOrders = Order::where('table_id', '=', $table_id)->
        whereIn('status',['Served'])->with('products')->get();

        if ($prepareOrders->isEmpty()) {
            return $this->error('No Served nor completed orders found for this table');
        }

        return OrderResource::collection($prepareOrders)
            ->additional(['message' => 'Prepared or Completed orders for table '.$table_id.' retrieved successfully']);
    }




            // change status for the waiter


    public function markOrderAsServed(int $orderId)
{
    $order = Order::find($orderId);
    if (!$order) {
        return response()->json(['error' => 'Order not found'], 404);
    }

    $order->status = 'served';
    if (!$order->save()) {
        return $this->error('Failed to update order status');
    }

    return $this->success('Order status updated to served');
}



        // change status fot the cashier

        public function markOrderAsPaid(int $orderId,Request $request)
        {
            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->status = 'paid';
            $order->payment_method=$request->method;
            if (!$order->save()) {
                return $this->error('Failed to update order status');
            }

            return $this->success('Order status updated to paid');
        }



        // change status for the kitchen

        public function changeOrderStatus(int $orderId)
        {
            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $allComplete = false;
            $allCanceled = false;
            $hasProgress = false;


            foreach ($order->products as $product) {
                if ($product->pivot->status === 'Complete') {
                    $allComplete = true;

                } elseif($product->pivot->status === 'Cancel')  {
                    $allCanceled = true;
                    event(new OrderProductCanceled($product, $order));

                 }  elseif($product->pivot->status === 'Progress') {
                    $hasProgress = true;
                }
            }

            if ($hasProgress) {
                return $this->error('Cannot change order status because there are order products with the "Progress" status');
            }


            if ($allComplete) {
                $order->status = 'Complete';
            } elseif ($allCanceled) {
                $order->status = 'Canceled';
             }

            if (!$order->save()) {
                return $this->error('Failed to update order status');

              }

            return $this->success('Order status updated');

            }



}
