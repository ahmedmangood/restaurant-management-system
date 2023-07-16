<?php
namespace App\Http\Controllers;

use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class CustomerAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:customers', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(CustomerLoginRequest $request){

        if (! $token = auth()->guard('customers')->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(CustomerRegisterRequest $request) {
        $customer = Customer::create(array_merge(
                    $request->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'Customer successfully registered',
            'user' => $customer
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->guard('customers')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->guard('customers')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->guard('customers')->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('customers')->factory()->getTTL() * 60,
            'user' => auth()->guard('customers')->user()
        ]);
    }
}
