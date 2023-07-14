<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserController extends Controller
{


    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function index()
    {
        try {
            $users = User::paginate();
            return UserResource::collection($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = bcrypt($request->password);

            $user = User::create($data);

            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create user'], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $auth =  $this->loginService->execute($credentials);
            return response()->json(['success' => 'user login', 'message' => $auth], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    public function me()
    {
        try {
            return response()->json(auth()->user(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    public function logout()
    {
        try {
            auth()->logout(true);
   
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], $e->getCode());
        }
    }


    
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user'], 500);
        }
    }

    public function update(StoreUserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();

            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }

            $user->fill($data);
            $user->save();

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

    public function delete(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }
}
