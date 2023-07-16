<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ingredients\StoreIngredientsRequest;
use App\Http\Requests\Ingredients\UpdateIngredientsRequest;
use App\Http\Resources\IngredientRescource;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use App\Traits\ApiRespone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IngredientController extends Controller
{
    use ApiRespone;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return IngredientResource::collection(Ingredient::paginate(8));
        //  $this->sendData('',IngredientResource::collection(Ingredient::paginate()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIngredientsRequest $request)
    {
       $data =  $request->all();
       return Ingredient::create($data) ?
        $this->success("Ingredient Created Successfully",Response::HTTP_CREATED) :
        $this->error('Ingredient Not Created '.Response::HTTP_CONFLICT);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $ingredient=Ingredient::find($id);
        if(!$ingredient){
            return $this->error('This Ingredient Not Exist');
        }
        return $this->sendData('',new IngredientResource($ingredient));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIngredientsRequest $request, int $id)
    {
        $ingredient=Ingredient::find($id);
        if(!$ingredient){
            return $this->error('This Ingredient Not Exist');
        }

        $data=$request->all();
        $ingredient->update($data);
        foreach ($ingredient->products as $product)
          {
            $product->UpdateStaus();
          }

       return $this->success("Ingredient Updates Successfully") ;
    //    $this->error('Ingredient Not Updated '.Response::HTTP_NOT_MODIFIED);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function changeStatus(int $id)
    {
        $ingredient=Ingredient::find($id);

        if(!$ingredient){
            return $this->error('This Ingredient  Not Exist');
        }

        $ingredient->status = !$ingredient->status;
        $ingredient->save();

        foreach ($ingredient->products as $product) {
               $product->UpdateStaus();
        }
        return $this->success("Ingredient Updates Successfully") ;
        }


    public function search(Request $request)
    {
        $keyword =$request->input('keyword','');
        return IngredientResource::collection(Ingredient::where('name','like',"%$keyword%")->paginate(8));
    }

    public function getActiveIngredients()
    {
        return IngredientResource::collection(Ingredient::where('status','=',1)->paginate(8));

    }
}
