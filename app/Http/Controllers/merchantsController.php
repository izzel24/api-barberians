<?php

namespace App\Http\Controllers;

use App\Models\merchants;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Validator;

class merchantsController extends Controller
{
    public function fetch(){
        $merchants = merchants::all();
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

}
