<?php

namespace App\Http\Controllers;

use App\Models\MerchantPhoto;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MerchantPhotoController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg', // maksimal 2MB
        ]);

        // Simpan file ke storage
        $path = $request->file('photo')->store('merchant_photos', 'public');

        $photo = MerchantPhoto::create([
            'merchant_id' => $request->merchant_id,
            'path' => $path,
        ]);

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'data' => $photo
        ], 201);
    }

    public function destroy($id)
{
    $photo = MerchantPhoto::findOrFail($id);


    // $merchant = $photo->merchant;

    // if (!$merchant || $merchant->user_id !== Auth::id()) {
    //     return response()->json([
    //         'message' => 'Unauthorized to delete this photo'
    //     ], 403);
    // }

    if (Storage::disk('public')->exists($photo->path)) {
        Storage::disk('public')->delete($photo->path);
    }


    $photo->delete();

    return response()->json([
        'message' => 'Photo deleted successfully'
    ], 200);
}

    



}
