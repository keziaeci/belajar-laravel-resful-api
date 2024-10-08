<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function register(UserRegisterRequest $request) : JsonResponse {
        $data = $request->validated();
        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    function login(UserLoginRequest $request) : UserResource {
        $request->validated();
        // dd($request->username);
        $user = User::where('username' , $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => 'username or password wrong'
                ]
            ],401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();
        return new UserResource($user);
    }

    function logout(Request $request) : JsonResponse {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    function getCurrentUser() {
        return new UserResource(Auth::user());
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
        $request->validated();
        $user =  Auth::user();
        if (isset($request->name)) {
            $user->name = $request->name;
        }
        if (isset($request->password)) {
            $user->password = bcrypt($request->password);
        }
        $user->save();
        // dd($request->all());

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
