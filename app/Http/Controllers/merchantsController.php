<?php

namespace App\Http\Controllers;

use App\Models\merchants;
use App\Models\Service;
use App\Models\User;
use Auth;
use DB;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Validator;

class merchantsController extends Controller
{
    public function fetch()
    {
        $merchants = merchants::with('user:id,email,phonenumber')->get();

        return response()->json($merchants);
    }

    public function destroy($id)
    {
        $merchant = merchants::find($id);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        $merchant->delete();

        return response()->json(['message' => 'Merchant deleted successfully']);
    }

    public function create(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'nik' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'description' => 'required|string',
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
            'city' => $request->city,
            'description' => $request->description,
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
            'city' => 'required|string|max:255',
            'description' => 'required|string'
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
                'city' => $request->city,
                'description' => $request->description,
                'status' => 'pending', // set ulang jadi pending setiap update
        ]);

        return response()->json([
            'message' => 'Merchant profile updated successfully!',
            'merchant' => $merchant
        ]);
    }

    public function fetchForCustomer()
    {
        $merchants = merchants::with(['photos'])
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

    public function showForMerchants()
{
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

    return response()->json([
        'user' => $user,
        'merchant' => $merchant,
    ]);
}

public function getAllUsersWithMerchantData()
{
    $users = User::with([
        'merchant.services',
        'merchant.operatingHours',
        'merchant.photos',
    ])->get();

    return response()->json([
        'message' => 'All users with merchant data fetched successfully.',
        'data' => $users,
    ]);
}


    public function destroyMerchant($id)
    {
        $merchant = merchants::find($id);

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        $merchant->delete();

        return response()->json(['message' => 'Merchant deleted successfully']);
    }

     public function getServicesbyId($merchantId)
    {
        $merchant = merchants::find($merchantId);

        if (!$merchant) {
            return response()->json(['error' => 'Merchant not found'], 404);
        }

        $services = $merchant->services;
        return response()->json($services);
    }

     public function getServices()
    {
        $user = Auth::user();

        $merchant = $user->merchant; // asumsi relasi: User hasOne Merchant

        if (!$merchant) {
            return response()->json(['error' => 'Merchant not found'], 404);
        }

        $services = $merchant->services; // asumsi relasi: Merchant hasMany Services

        return response()->json($services);
    }

    public function storeService(Request $request){
        $user = auth()->user();

        $merchant = merchants::where('user_id', $user->id)->first();

         if (!$merchant) {
        return response()->json(['message' => 'You are not registered as a merchant'], 403);
        }

         $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'duration' => 'nullable|integer|min:0',
        ]);

        $service = $merchant->services()->create($validated);

        return response()->json([
            'message' => 'Service created successfully',
            'data' => $service
        ], 201);

    }

    public function getOperatingHours($merchant_id)

    {
    $merchant = merchants::find($merchant_id);

    if (!$merchant) {
        return response()->json(['message' => 'Merchant not found'], 404);
    }

    $operatingHours = $merchant->operatingHours;

    return response()->json([
        'merchant' => $merchant->company_name,
        'operating_hours' => $operatingHours,
    ]);
    }

    public function getOperatingHoursByAuth()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Ambil merchant berdasarkan user_id
    $merchant = merchants::where('user_id', $user->id)->first();

    if (!$merchant) {
        return response()->json(['message' => 'Merchant not found'], 404);
    }

    $operatingHours = $merchant->operatingHours;

    return response()->json([
        'merchant' => $merchant->company_name,
        'operating_hours' => $operatingHours,
    ]);
}




    
public function updateProfile(Request $request)
{
    $user = auth()->user();

    // Validasi data sesuai kebutuhan
    $validated = $request->validate([
        'username' => 'sometimes|string|max:255',
        'email' => 'sometimes|email',
        'phone' => 'sometimes|string|max:20',
        'company_name' => 'sometimes|string|max:255',
        'company_address' => 'sometimes|string|max:255',
        'city' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        // Update user
        $user->update([
           'username' => $validated['username'] ?? $user->username,
            'email' => $validated['email'] ?? $user->email,
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        // Update merchant (pastikan relasi user <-> merchant 1-1)
        $merchant = $user->merchant;
        if ($merchant) {
            $merchant->update([
                'company_name' => $validated['company_name'] ?? $merchant->company_name,
                'company_address' => $validated['company_address'] ?? $merchant->company_address,
                'city' => $validated['city'] ?? $merchant->city,
                'description' => $validated['description'] ?? $merchant->description,
            ]);
        }

        DB::commit();
        return response()->json(['message' => 'Profile updated successfully.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
    }


    
}


  public function updateService(Request $request, $id)
{
    $request->validate([
        'name' => 'sometimes|string',
        'description' => 'sometimes|nullable|string',
        'price' => 'sometimes|numeric',
        'duration' => 'sometimes|integer|min:1', // tambahkan durasi validasi
    ]);

    $merchant = Auth::user()->merchant;
    $service = Service::where('id', $id)->where('merchant_id', $merchant->id)->first();

    if (!$service) {
        return response()->json(['error' => 'Service not found or not authorized'], 404);
    }

    $service->update($request->only(['name', 'description', 'price', 'duration'])); // tambahkan duration di sini juga

    return response()->json(['message' => 'Service updated successfully', 'data' => $service]);
}


    public function deleteService($id)
{
    $merchant = Auth::user()->merchant;
    $service = Service::where('id', $id)->where('merchant_id', $merchant->id)->first();

    if (!$service) {
        return response()->json(['error' => 'Service not found or not authorized'], 404);
    }

    $service->delete();

    return response()->json(['message' => 'Service deleted successfully']);
}
}
