<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locations;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::withCount('tasks')->paginate(request('per_page'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $request->merge(['password' => bcrypt($request->password)]);
        
        $user->fill($request->only($user->getFillable()))->save();

        return $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if($request->password) {
            $request->merge(['password' => bcrypt($request->password)]);
        }else{
            $request->merge(['password' => $user->password]);
        }
        $user->fill($request->only($user->getFillable()))->save();

        return $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response(['message' => 'User has been deleted!']);
    }
}
