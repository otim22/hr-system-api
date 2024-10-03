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
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(): JsonResponse
    {
        $staff = Staff::all();
        return $this->sendResponse(StaffResource::collection($staff), 'Staff retrieved successfully.');
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
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
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
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
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
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
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
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
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(Staff $staff): JsonResponse
    {
        $staff->delete();
        return $this->sendResponse([], 'Staff deleted successfully.');
    }
}
