<?php

namespace App\Http\Controllers;

use App\Traits\ApiRespone;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Stripe;

class PaymentController extends Controller
{
    use ApiRespone;
    // public function checkout(Request $req){
    //     // return $req->all();
    //     Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    //     Stripe\Charge::create([
    //         'amount'=>1000,
    //         'currency' => 'EGP',
    //         'source' => $req->stripe->token,
    //         'description'=>'Test'
    //     ]);
    //     Session::flash('success','payment has been successfully');
    //     return $this->success('payment has been successfully done');
    // }
    public function createCheckoutSession(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

// dd($request);

        try {
            $session = \Stripe\Checkout\Session::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'EGP',
                            'product_data' => [
                              'name' => 'T-shirt',
                              'images'=>['https://www.designmantic.com/blog/wp-content/uploads/2015/11/Item-Logo.jpg'],
                              'description'=>'Total price of all products in the cart',
                            // 'price'=>$request->price
                            ],
                            'unit_amount' => (int)$request->price*100,
                          ],
                          'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'success_url' => $request->successUrl,
                'cancel_url' => $request->failureUrl,
            ]);

            return response()->json([
                'sessionId' => $session->id,
                'PublicKey' => env('STRIPE_KEY')
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
