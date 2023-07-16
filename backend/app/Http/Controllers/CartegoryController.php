<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Traits\ApiRespone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Media;
use App\Models\Category;
use App\Models\Product;
use Faker\Core\Number;
use GuzzleHttp\Psr7\UploadedFile;
use Ramsey\Uuid\Type\Integer;

use function PHPUnit\Framework\returnSelf;

class CartegoryController extends Controller
{
    use ApiRespone;
    /*
    ** Display All Categories
    */
    public function index()
    {
        $categories = Category::paginate(8);
        return CategoryResource::collection($categories)
            ->additional(['message' => 'All Categories has been retrieved','active_categories'=>$this->showActive()]);
    }

    /*
    ** Display Specific Category According to search keyword
    */
    public function show(Request $req)
    {
        $filtered = DB::table('categories')->select(['name', 'image','id'])->where('name', 'like', $req["name"] . '%')->get();
        //check if the filtered array contains items or not
        return $filtered->first() ?
            $this->sendData('', $filtered) :
            $this->error('No category with this name');
    }

    /*
    ** Create Category
    */
    public function store(StoreCategoryRequest $req)
    {
        $data = $req->except('image');

        $data['image'] = Media::upload($req->image, 'categories');  //the hashname of the image is not working so i use the original name of the image

        $data['created_at'] = now();

        if (DB::table('categories')->insert($data)) {
            return $this->sendData('Category has been stored successfully', $data); // return the list of categories
        }
    }


    /*
    ** Edit category to return the data of this category
    */
    public function edit(Category $category)
    {
        return $this->sendData('', new CategoryResource($category));
    }

    /*
    ** Update Category
    */
    public function update(UpdateCategoryRequest $req, Category $category)
    {

        $data = $req->except('image', '_method');
        if ($req->hasFile('image')) {
            $imageName = Media::upload($req->file('image'), 'categories');
            $data['image'] = $imageName;
            Media::delete($category->image);
        }

        if (DB::table('categories')->where('id', $category->id)->update($data))
            return $this->success('Category updated successfully');
        return $this->success('Category is not being updated');
    }

    /*
    ** Change Category Status
    */
    public function changeStatus(Request $req ,$category)
    {
        $data = $req->except('_method');

        if(!isset($data['status']))
            return $this->error('status is required');

        $filteredproducts = DB::table('products')->select('status')->where('category_id', '=', $category)->get();
        DB::table('categories')->where('id', $category)->update(['status' => $data['status']]);


        if ($filteredproducts->all()){
            $products = Product::where('category_id',$category)->get();
            foreach($products as $product)
            {
                DB::table('products')->where('category_id',$category)->update(['closed'=> !(integer)$data['status']]);

                $product->UpdateStaus();
            }
        }


        return $this->sendData('Category now is '.((integer)$data['status']? 'on' : 'off'),$category);
    }


    /*
    ** Delete Category
    */
    public function destroy(Category $category)
    {
        $filteredproducts = DB::table('products')->select('*')->where('category_id', '=', $category->id)->get();
        DB::table('categories')->where('id', $category->id)->update(['status' => '0']);

        if (sizeof($filteredproducts->all()) > 0) {
            DB::table('products')->where('category_id',$category->id)->update(['closed'=> 1,'status'=>'0']);
            return $this->success('Category cannot be deleted, but it\'s now unavialable',);
        } else {
            $category->delete();
            Media::delete($category->image);
            return $this->success('Category Deleted successfully',);
        }
    }

    /**
     * Display active categories
     */
    public function showActive()
    {
        return DB::table('categories')->
        where('status',1)->
        select('name','image','id')->
        get();
    }

}
