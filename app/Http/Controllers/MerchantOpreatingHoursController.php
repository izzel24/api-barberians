<?php

namespace App\Http\Controllers;

use App\Models\MerchantOperatingHour;
use Auth;
use Illuminate\Http\Request;

class MerchantOpreatingHoursController extends Controller
{
     public function index()
    {
        $merchant = Auth::user()->merchant;

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        return response()->json(MerchantOperatingHour::where('merchant_id', $merchant->id)->get());
    }

    public function store(Request $request)
    {
        $merchant = Auth::user()->merchant;

        $request->validate([
            'day_of_week' => 'required|string',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
        ]);

        $data = $request->only(['day_of_week', 'open_time', 'close_time']);
        $data['merchant_id'] = $merchant->id;

        $hour = MerchantOperatingHour::create($data);

        return response()->json(['message' => 'Operating hour created successfully', 'data' => $hour]);
    }

    public function update(Request $request, $id)
    {
        $hour = MerchantOperatingHour::find($id);

        // if (!$hour || $hour->merchant_id !== Auth::user()->merchant->id) {
        //     return response()->json(['message' => 'Not authorized or not found'], 403);
        // }

        $hour->update($request->only(['day_of_week', 'open_time', 'close_time']));

        return response()->json(['message' => 'Operating hour updated successfully']);
    }

    public function destroy($id)
    {
        $hour = MerchantOperatingHour::find($id);

        // if (!$hour || $hour->merchant_id !== Auth::user()->merchant->id) {
        //     return response()->json(['message' => 'Not authorized or not found'], 403);
        // }

        $hour->delete();

        return response()->json(['message' => 'Operating hour deleted successfully']);
    }
}
