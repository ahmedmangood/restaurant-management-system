<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use App\Http\Resources\Table\TableResource;
use Illuminate\Http\Response;
use App\Traits\ApiRespone;



class TableController extends Controller
{
    use ApiRespone;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::paginate(8);
        return TableResource::collection($tables)
        ->additional(['message' => 'Tables Retrieved Successfully']);
    }


    public function getAvailableTables()
{
    $status = 1; // only show available tables

    $tables = Table::where('status', $status)->paginate(8);

    return TableResource::collection($tables)
        ->additional(['message' => 'Available Tables Retrieved Successfully']);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTableRequest $request)
    {
        $data= $request->all();

        if ( Table::create($data)){
            return $this->success('Table added successfully',Response::HTTP_CREATED);

         }
         return $this->error('Table not added ',Response::HTTP_CONFLICT);

       
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $table=Table::find($id);

        if(!$table){
            return $this->error('table not Exist');
        }
        return $this->sendData('',new TableResource($table));  
      }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTableRequest $request, int $id)
    {
        $table=Table::find($id);
        
        if(!$table){
            return $this->error('table not Exist');
        }  
        $data = $request->all();
        if ($table->update($data)){
            return $this->success('table Updated successfully', Response::HTTP_OK);
           } 
           $this->error('table Not Updated '.Response::HTTP_NOT_MODIFIED);
    
    
    }

    public function getTablesWithServedOrders()
    {
        $status = 'served';
        $tables = Table::whereHas('orders',function ($query) use ($status){

            $query->where('status',$status);
        })->get();

        if ($tables->isEmpty()){
            return $this->success('No Tables have served orders');

        }
        return TableResource::collection($tables)
        ->additional(['message' => 'Tables with Served orders retrieved successfully']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function changeStatus(int $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return $this->error('Table not found.', Response::HTTP_NOT_FOUND);
        }
        $reservations = $table->reservations;
        
        foreach ($reservations as $reservation) {
            if ($reservation->status !== 'completed' && $reservation->status !== 'canceled') {
                return $this->error('Cannot change table status: there are reservations on this table', Response::HTTP_BAD_REQUEST);
            }
        }
    
        $table->status = !$table->status;
        $table->save();
    
        return $this->success('Table status updated successfully.');
        
    }


    public function availbeTablesInTheDay(Request $request)
    {
        $today = date('Y-m-d');
        $tables = Table::with('orders')->whereHas('orders',function($query) use ($today)
        {

            $query->whereDate('created_at',$today)->whereIn('status',['paid','canceled','pending']);

        })->orWhereDoesntHave('orders')->get();
        return $this->sendData('availbe Tables In The Day', TableResource::collection($tables));
    }


    public function searchByGuestNumbers(Request $request)
{
    $query = $request->input('quest');


    $tables = Table::where('guest_numbers', '=', $query)->get();

    if($tables->isEmpty()){

        return $this->error('No Table with this guest numbers');
    }
    return TableResource::collection($tables)
        ->additional(['message' => 'Tables Retrieved Successfully']);
}

}
