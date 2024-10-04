<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\StaffResource;
use App\Http\Controllers\API\BaseController as BaseController;

class StaffController extends BaseController
{
    /**
    * @OA\Get(
    *     path="/api/staff",
    *     operationId="getStaff",
    *     tags={"Get all staff"},
    *     summary="Get all staff",
    *     description="Get all staff Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Staff Retrieved Successfully",
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
    public function index(): JsonResponse
    {
        $staff = Staff::all();
        return $this->sendResponse(StaffResource::collection($staff), 'Staff Retrieved Successfully.');
    }

    /**
    * @OA\Post(
    *     path="/api/staff",
    *     operationId="registerStaff",
    *     tags={"Register staff"},
    *     summary="creates new staff",
    *     description="Staff Registration Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"surname","other_names","date_of_birth"},
    *                 @OA\Property(property="surname",type="text"),
    *                 @OA\Property(property="other_names",type="text"),
    *                 @OA\Property(property="date_of_birth",type="date"),
    *                 @OA\Property(property="image_src",type="text"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *         response="201",
    *         description="Staff Registered Successfully",
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
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'surname' => 'required|string',
            'other_names' => 'required|string',
            'date_of_birth' => 'required|date',
            'image_src' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $staff = Staff::create($input);
        $unique_code = mt_rand(1000000000, 9999999999);
        $staff->unique_code = $unique_code;

        if($request->hasFile('image_src')) {
            $filename = $request->file('image_src')->getClientOriginalName(); 
            $getFileNameWitouText = pathinfo($filename, PATHINFO_FILENAME);
            $getFileExtension = $request->file('image_src')->getClientOriginalExtension(); 
            $createNewFileName = time() . '_' . str_replace(' ', '_', $getFileNameWitouText) . '.' . $getFileExtension;
            $img_path = $request->file('image_src')->move('storage/images', $createNewFileName);
            $staff->image_src = $createNewFileName; 
        }

        $staff->save();

        return $this->sendResponse(new StaffResource($staff), 'Staff created successfully.');
    } 

    /**
    * @OA\Get(
    *     path="/api/staff/{id}",
    *     operationId="getStaffById",
    *     tags={"Get specific staff"},
    *     summary="Get staff by id",
    *     description="Get staff by id Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"id"},
    *                 @OA\Property(property="id",type="string"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Staff Retrieved Successfully",
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
    public function show($id): JsonResponse
    {
        $staff = Staff::find($id);

        if (is_null($staff)) {
            return $this->sendError('Staff not found.');
        }

        return $this->sendResponse(new StaffResource($staff), 'Staff retrieved successfully.');
    }

    /**
    * @OA\Put(
    *     path="/api/staff/{staff}",
    *     operationId="updateStaff",
    *     tags={"Update staff"},
    *     summary="Update staff details",
    *     description="Update staff Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"date_of_birth"},
    *                 @OA\Property(property="date_of_birth",type="date"),
    *                 @OA\Property(property="image_src",type="text"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Staff Updated Successfully",
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
    public function update(Request $request, Staff $staff): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'unique_code' => 'nullable',
            'date_of_birth' => 'nullable',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if (!empty($input['unique_code'])) {
            if (!$staff->is_verified && $input['unique_code'] == $staff->unique_code) {
                $staff->is_verified = true;
                $staff->employee_number = 'EN-' . mt_rand(1000, 9999);
                $staff->save();
            } elseif ($staff->is_verified && $input['unique_code'] == $staff->unique_code) {
                return $this->sendError('Your are already verified.');
            } elseif($input['unique_code'] != $staff->unique_code) {
                return $this->sendError('Wrong Code.'); 
            }
        }

        if (!empty($input['date_of_birth'])) {
            $staff->date_of_birth = $input['date_of_birth'];
        }

        $staff->save();

        return $this->sendResponse(new StaffResource($staff), 'Staff updated successfully.');
    }

    /**
    * @OA\Post(
    *     path="/api/imageUpload/{id}",
    *     operationId="updateStaffImageById",
    *     tags={"Update staff image"},
    *     summary="Update staff image by id",
    *     description="Get staff image by id Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"id"},
    *                 @OA\Property(property="id",type="string"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Staff Image Updated Successfully",
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
    public function imageUpload(Request $request, $id): JsonResponse
    {
        $staff = Staff::find($id);
        $validatedData = Validator::make($request->all(), [
            'image_src' => 'required|mimes:jpg,jpeg,png|max:3048',
        ]);
        
        if ($validatedData->fails()) {
            return Response::json(['success' => false, 'message' => $validatedData->errors()], 400);
        }
        
        if($request->hasFile('image_src')) {
            $filename = $request->file('image_src')->getClientOriginalName(); 
            $getFileNameWitouText = pathinfo($filename, PATHINFO_FILENAME);
            $getFileExtension = $request->file('image_src')->getClientOriginalExtension(); 
            $createNewFileName = time() . '_' . str_replace(' ', '_', $getFileNameWitouText) . '.' . $getFileExtension;
            $img_path = $request->file('image_src')->move('storage/images', $createNewFileName);
            $staff->image_src = $createNewFileName; 
        }

        if ($staff->save()) {
            return $this->sendResponse(new StaffResource($staff), 'Staff Image Updated Successfully.');
        } else {
            return $this->sendError('Image Not Successfully Uploded'); 
        }
    }

    /**
    * @OA\Delete(
    *     path="/api/staff/{staff}",
    *     operationId="deleteStaffByObject",
    *     tags={"Delete staff"},
    *     summary="Delete staff by object",
    *     description="Deletes staff by object Endpoint",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 type="object",
    *                 required={"staff"},
    *                 @OA\Property(property="staff",type="string"),
    *             ),
    *         ),
    *     ),
    *     @OA\Response(
    *       response="200",
    *       description="Staff deleted Successfully",
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
    public function destroy(Staff $staff): JsonResponse
    {
        $staff->delete();
        return $this->sendResponse([], 'Staff deleted successfully.');
    }
}
