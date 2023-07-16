<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Traits\ApiRespone;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiRespone;
    private $user_id ;
    public function __construct() {
        $this->user_id = auth()->user()->id;
    }
    /**
     * Display all stored products
     */
    public function index()
    {
        // dd($this->user_id);
        $carts = Cart::where('user_id', $this->user_id)->first();

        if(!$carts) {
            return $this->success('no cards to be showen');
        }

        $cart = $carts->cartProduct()->with('product.ingredients')->get();

        return $this->sendData('All Carts has been retrieved',[$cart,'cart_total_price' => $carts->total_price]);
    }

    /**
     * Store product in cart
     */
    public function store(StoreCartRequest $req)
    {
        // dd($this->user_id);

        //check if this product already exists or not
        if (DB::table('cart_product')->where('product_id', '=', $req['product_id'])->where('user_id', '=', $this->user_id)->exists()) {
            return $this->error('this product is already in the cart');
        }
        // Get the product information
        $product = Product::findOrFail($req['product_id']);

        // Create a new cart item
        $cart_item = CartProduct::create([
            'user_id' => $this->user_id,
            'product_id' => $req['product_id'],
            'total_price' => $product->total_price,
            'quantity' => 1,
            'created_at' => now(),
        ]);

        // Calculate the total price on the cart
        $total_price_on_cart = $this->countTotalPrice($this->user_id);

        // Return a success response
        return $this->sendData('Product has been added to the cart.', $total_price_on_cart);
    }

    /**
     * Update card quantity
     */
    public function update(UpdateCartRequest $req)
    {
        //get the card that the user interact with
        $cartproduct = CartProduct::with('product.ingredients')->findOrFail( $req['id']);
        //get the ingredients of this product
        $productIngredients = $cartproduct->product->ingredients;
        //recieve the quantity of the product that user need
        $cardQTY = $req->quantity;
        //check if the user try to decrement the quantity to 0 or less and send to him error message
        if($cardQTY < 1)
        {
            return $this->error('The quantity can not be decremented less than 1');
        }
        //loop in the ingredients to check if this ingredients still exist in the stoke with this demand quantity
        foreach ($productIngredients as $ingredient) {
            //if not so send error message to the user that this product cannot increment more
            if ($ingredient->quntity <  $cardQTY *  $ingredient->pivot->quantity)
                return $this->error("This product cannot be increased any more");
        }
        //else this quantity is avialable so update the quantity in the cartproduct table and the price of this product
        $cartproduct->update([
            'quantity' => $cardQTY,
            'total_price' => $cardQTY * $cartproduct->product->total_price
        ]);
        //count the total price of all demand products
        $this->countTotalPrice($this->user_id);
        //send success message to the user tell him that the cart product quantity has been updated
        return $this->success('The quantity has been updated');

    }

    /**
     * Destroy one card or all cards
     */
    public function destroy(Request $request)
    {
        if($request['id']){
            $cart_product =  DB::table('cart_product')->where('id',$request['id'])->exists();
            if(!$cart_product){
                return $this->error("This Product Doesn't Exist In The Cart");
            }
            DB::table('cart_product')->where('cart_product.id', $request['id'])->delete();

            $total_price_on_cart = $this->countTotalPrice($this->user_id);

            if ($total_price_on_cart == 0) {

                DB::table('carts')->where('user_id', '=', $this->user_id)->delete();

                return $this->success('No Products In The Cart');
            }

            DB::table('carts')->where('user_id', '=', $this->user_id)->update([
                'total_price'=>$total_price_on_cart,
                'updated_at' => now()
            ]);

            return $this->success('Product Deleted Successfully From Cart');
        }
        DB::table('cart_product')->where('cart_product.user_id', $this->user_id)->delete();

        DB::table('carts')->where('user_id', '=', $this->user_id)->delete();

        return $this->success('No Products In The Cart');

    }

    /**
     * Count the total price of all cards
     */
    public static function countTotalPrice($user_id)
    {
        //get all products belong to specific user then sum all prices of all choosed products
        $totalprice = DB::table('cart_product')->where('user_id', $user_id)
            ->sum('total_price');
        if (Cart::where('user_id', $user_id)->exists()) {
            DB::table('carts')->where('user_id', $user_id)->update(['total_price' => $totalprice, 'updated_at' => now()]);
        } else {
            Cart::insert([
                'total_price' => $totalprice,
                'user_id' => $user_id,
                'created_at' => now()
            ]);
        }
        return $totalprice;
    }
}
