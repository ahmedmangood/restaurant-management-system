<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Services\Media;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;




use App\Http\Resources\User\UserResource;
use App\Traits\ApiRespone;
// use App\Http\Services\Media;


class UserController extends Controller
{
    use ApiRespone;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        
        $users = User::paginate(8);
        return UserResource::collection($users)
        ->additional(['message' => 'Users Retrieved Successfully']);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
 
        $data = $request->except('image');
        if ($request->hasFile('image')) {
        $data['image'] = Media::upload($request->image, 'users');
        }

         if (User::create($data)){
            return $this->success('User added successfully',Response::HTTP_CREATED);

         }
         return $this->error('User not added ',Response::HTTP_CONFLICT);



   }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        
        $user=User::find($id);

    if(!$user){
        return $this->error('user not Exist');
    }
    return $this->sendData('',new UserResource($user));

        

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $user=User::find($id);
        
        if(!$user){
            return $this->error('user not Exist');
        }
        $loggedInUser = auth()->user();

        if ($loggedInUser->role != 'Admin'){
            if ($loggedInUser->id != $user->id){
                return $this->error('You do not have permission to update this user');
            }
        
        $data = $request->except('image','role');
    } else {
        $data = $request->except('image');
    }
        if ($request->hasFile('image')) 
        {
            if ($user->image) {
                Media::delete($user->image);        
                }
            
            $image = $request->file('image');
            $data['image'] = Media::upload($image, 'users');
        }

       if ($user->update($data)){
        return $this->success('User Updated successfully', Response::HTTP_OK);
       } 
       $this->error('User Not Updated '.Response::HTTP_NOT_MODIFIED);



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {

        $user = User::find($id);
    
        if (!$user) {
            return $this->error('User not found');
        }
    
        if ($user->image) {
            Media::delete($user->image);
        }
        $user->delete();
        return $this->success('User Deleted successfully',);
}




/**
 * Search for users by name.
 */
public function search(Request $request)
{
    $name = $request->query('name');
    $users = User::where('name', 'like', '%'.$name.'%')->paginate(8);

    if ($users->isEmpty()){
        return $this->error('No users found with this name',Response::HTTP_NOT_FOUND);
    }
    return UserResource::collection($users)
        ->additional(['message' => 'Users Retrieved Successfully']);
}


}