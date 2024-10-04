<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;

class RegisterController extends BaseController
{
    /**
    * @OA\Post(
    *     path="/api/register",
    *     operationId="registerUser",
    *     tags={"Register"},
    *     summary="Register a new user",
    *     description="User Registration Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"name","email","password","password_confirmation"},
    *                 @OA\Property(property="name",type="text"),
    *                 @OA\Property(property="email",type="text"),
    *                 @OA\Property(property="password",type="password"),
    *                 @OA\Property(property="password_confirmation",type="password"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *         response="201",
    *         description="User Registered Successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Registered Successfull",
    *       @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response="422",
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response="400",
    *         description="Bad Request",
    *         @OA\JsonContent()
    *     ),
    * )
    */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        
        return $this->sendResponse($success, 'User Registration Successfully.');
    }

    //  Login API
    /**
    * @OA\Post(
    *     path="/api/login",
    *     operationId="loginUser",
    *     tags={"Login"},
    *     summary="Login a user",
    *     description="User Login Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"email","password"},
    *                 @OA\Property(property="email",type="text"),
    *                 @OA\Property(property="password",type="password"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *         response="201",
    *         description="User Login Successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Login Successfull",
    *       @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response="422",
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response="400",
    *         description="Bad Request",
    *         @OA\JsonContent()
    *     ),
    * )
    */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User Login Successfully.');
        } else { 
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        } 
    }
}
