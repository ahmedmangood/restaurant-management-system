<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductIngredientRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Services\Media;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Traits\ApiRespone;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Console\Input\Input;

class ProductController extends Controller
{
    use ApiRespone;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with('category')->paginate(8,['id','name','image','status','closed','total_price','category_id']);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        // info product info -> ingrdents[id => quntity this product need] , extra [ids]

        $data = $request->except('ingredients');
       $this->checkCategory($request->category_id);

        $product = new Product;
        $product->name = $data['name'];
        $product->category_id = $request->category_id;
        $product->total_price = $request->total_price;
        $product->extra=$request->input('extra',null);
        $product->discount = $request->input('discount',null);
        $product->description = $request->input('description',null);

        $image_name =  Media::upload($request->image,'products');
        $product->image = $image_name;

        $product->save();
        $product->UpdateStaus();
       if(  $this->addIngredientToProduct($request,$product)){
        return $this->success('Product Add Succesfully');
       }
       return $this->error('Product Not Add Succesfully');

    }



    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $product = Product::with(['ingredients','category'])->find($id);

        if(!$product){
            return $this->error('This Product Not Exist');
        }

        if( $product->extra)
        {
            $extras= [];
            foreach($product->extra as $extra){
               $extras[$extra]=[ "name"=> Ingredient::where('id',$extra)->pluck('name')[0] , "id"=>$extra];
            }
            $product->extra = $extras;
        }
        return $this->sendData('',$product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $product = Product::find($id);
        if(!$product){
            return $this->error('This Product Not Exist');
        }
        $this->checkCategory($request->category_id);

        $data = $request->except('image');
        if($request->hasFile('image'))
        {
            Media::delete($product->image);
            $path=  Media::upload($request->image,'products');
            $data['image']=$path;
        }
       if( $product->update($data))
       {
        $product->UpdateStaus();
        return $this->sendData('Product Update Succesfully',$product);
       }

    }

    public function search(Request $request)
    {
        $keyword =$request->input('keyword','');
        return $this->sendData('',Product::where('name','like',"%$keyword%")->paginate(8));
    }

    public function getActiveProducts()
    {
        return $this->sendData('',Product::where('status','=',1)->where('closed',0)->paginate(8));
    }

    protected function checkCategory(int $categoryId)
    {
        $category=Category::find($categoryId);
        if(!$category->status && empty($category->products[0])) // if assgin first product to ctegory make category available
        {
            $category->status =1;
            $category->save();
        }
    }

    private function addIngredientToProduct($request,$product)
    {

        $ingredientsData = [];
        foreach ($request->ingredients as $ingredientData) {
            $ingredientId = $ingredientData['id'];
            $quantity = $ingredientData['quantity'];
            $total = $ingredientData['total'] ;
            $price = $ingredientData['total'] / $ingredientData['quantity'];
            $ingredientsData[$ingredientId] = compact('quantity', 'total', 'price');
        }
        return  $product->ingredients()->sync($ingredientsData);
    }
    public function updateIngredientsForProduct(UpdateProductIngredientRequest $request,int $id)
    {
        $product = Product::find($id);
        if(  $this->addIngredientToProduct($request,$product)){
            return $this->success('Product Ingredients Updated Succesfully');
           }
           return $this->error('Product Ingredients Not Update Succesfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function changeClosed(int $id)
    {
        $porduct = Product::find($id);

        if (!$porduct) {
            return $this->error('This Product Not Exist');
        }
        if(!$porduct->status){
            return $this->error("Can't Edit in Status , Category Or Ingridents Is Closed");
        }
        $porduct->closed=!$porduct->closed;
        if ($porduct->save()) {
            return $this->success("Product Updated Successfully");
        } else {
            return $this->error('Product Not Updated ', Response::HTTP_NOT_MODIFIED);
        }
    }

    public function getProductsByCategoryId(int $id)
    {
        $categoty = Category::find($id);
        if(!$categoty){

            return $this->error('This Category Not Exist');
        }

        $products = Product::where('status','=',1)->where('closed',0)->where('category_id',$id)->paginate(8);

        if($products){
            return $this->sendData('',$products);
        }
        return $this->success('No Product In This Category');
    }





}
