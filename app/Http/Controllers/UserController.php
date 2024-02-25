<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
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
        if (!$request->clerk_id) return response([
            'message' => 'Please signup/login to update your user information',
        ], 401);
        // if (User::where('phone', $request->phone)
        //     ->whereNotNull('phone_verified_at')->first()
        // ) {
        //     return response([
        //         'message' => 'Phone number has already been registered',
        //     ], 400);
        // }
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $validated['phone_verified_at'] = now();
        $user =  User::updateOrCreate(
            [
                'phone' => $validated['phone'],
            ],
            $validated
        );
        if (!$user->phone_verified_at)
            $user->phone_verified_at = now();

        $clerk_id = 'user_2ccWrxZzA9xhRkQ6SAlZFHeqnJM';
        if ($request->clerk_id) {
            $clerk_id = $request->clerk_id;
        }
        $token = env('CLERK_SECRET_KEY');
        $httpRequest = Http::withHeaders([
            'Authorization' => "Bearer $token",
        ]);
        $data = [
            "external_id" => $user->id,
            "first_name" => $request->name,
            "last_name" => $request->name,
            "username" => $request->username,
            "password" => $request->password,
            "skip_password_checks" => true,
            "sign_out_of_other_sessions" => true,
            "delete_self_enabled" => true,
            "create_organization_enabled" => true,
        ];
        // ->get('https://api.clerk.com/v1/users/user_2ccWrxZzA9xhRkQ6SAlZFHeqnJM');
        $response = $httpRequest->patch('https://api.clerk.com/v1/users/' . $clerk_id, $data);
        return $response;
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
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->clerk) {
            $user->phone_verified_at = now();
            $user->save();
            return $user;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
