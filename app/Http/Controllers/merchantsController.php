<?php

namespace App\Http\Controllers;

use App\Models\merchants;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Validator;

class merchantsController extends Controller
{
    public function fetch(){
        $merchants = merchants::with('user:id,email,phonenumber')->get();

    return response()->json($merchants);
    }
    public function destroy($id){
        $merchant = merchants::find($id);

        if(!$merchant){
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        $merchant->delete();

        return response()->json(['message' => 'Merchant deleted successfully']);
    }

    public function create(Request $request){


        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'nik' => 'required|string|max:20',
            'status' => 'nullable|in:pending,approved,rejected', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        $merchant = merchants::create([
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'nik' => $request->nik,
            'status' => 'pending',  
            'user_id' => $user->id,  
        ]);
        
        return response()->json([
            'message' => 'Merchant profile created successfully!', 
            'merchant' => $merchant
        ]); 
    }

    public function updateStatus(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:approved,rejected',
        'rejection_reason' => 'required_if:status,rejected|string|nullable',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $merchant = merchants::find($id);

    if (!$merchant) {
        return response()->json(['message' => 'Merchant not found'], 404);
    }

    $merchant->status = $request->status;

    if ($request->status === 'rejected') {
        $merchant->rejection_reason = $request->rejection_reason;
    }

    $merchant->save();

    return response()->json([
        'message' => "Merchant status updated to {$request->status}.",
        'merchant' => $merchant
    ]);
}

public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'company_name' => 'required|string|max:255',
        'company_address' => 'required|string',
        'nik' => 'required|string|max:20',
        // status tidak perlu divalidasi dari request karena kita akan set manual
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $user = auth()->user();

    $merchant = merchants::where('user_id', $user->id)->first();

    if (!$merchant) {
        return response()->json(['message' => 'Merchant not found'], 404);
    }

    $merchant->update([
        'company_name' => $request->company_name,
        'company_address' => $request->company_address,
        'nik' => $request->nik,
        'status' => 'pending', // set ulang jadi pending setiap update
    ]);

    return response()->json([
        'message' => 'Merchant profile updated successfully!',
        'merchant' => $merchant
    ]);
}

public function fetchForCustomer() {
    $merchants = merchants::with([  'photos'])
        ->get()
        ->map(function ($merchant) {
            return [
                'id' => $merchant->id,
                'company_name' => $merchant->company_name,
                'company_address' => $merchant->company_address,
                'status' => $merchant->status,
                'city' => $merchant->city,
                'description' => $merchant->description,
                'services' => $merchant->services,
                'operating_hours' => $merchant->operatingHours,
                'photos' => $merchant->photos,
            ];
        });

    return response()->json($merchants);
}

public function showForCustomer($id)
{
    $merchant = merchants::with(['photos', 'services', 'operatingHours'])
        ->find($id);

    if (!$merchant) {
        return response()->json(['message' => 'Merchant not found'], 404);
    }

    return response()->json([
        'id' => $merchant->id,
        'company_name' => $merchant->company_name,
        'company_address' => $merchant->company_address,
        'status' => $merchant->status,
        'city' => $merchant->city,
        'description' => $merchant->description,
        'services' => $merchant->services,
        'operating_hours' => $merchant->operatingHours,
        'photos' => $merchant->photos,
    ]);
}

public function showForMerchants(){
     $user = auth()->user();

    if ($user->role !== 'merchant') {
        return response()->json(['message' => 'Access denied.'], 403);
    }

    $merchant = merchants::with(['services', 'operatingHours', 'photos'])
        ->where('user_id', $user->id)
        ->first();

    if (!$merchant) {
        return response()->json(['message' => 'You have not registered as a merchant.'], 404);
    }

    return response()->json($merchant);
}

}
