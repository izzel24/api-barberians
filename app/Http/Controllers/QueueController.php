<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdated;
use App\Models\Queue;
use App\Models\Service;
use Auth;
use Illuminate\Http\Request;
use Log;

class QueueController extends Controller
{
  public function store(Request $request)
{
    try {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'time' => 'required',
            'payment_method' => 'required|in:cash,transfer,virtual_account', // tambahkan validasi
        ]);

        $service = Service::findOrFail($request->service_id);

        $queue = Queue::create([
            'user_id' => Auth::id(),
            'merchant_id' => $request->merchant_id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
            'total_price' => $service->price,
            'payment_method' => $request->payment_method, // simpan di sini
        ]);

        return response()->json([
            'message' => 'Queue created',
            'data' => $queue,
        ], 201);
    } catch (\Exception $e) {
        Log::error('Queue Store Error:', ['message' => $e->getMessage()]);
        return response()->json([
            'message' => 'An error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }

}

    // 2. Get user's queues
    public function userQueues()
    {
        $queues = Queue::with('merchant', 'service')->where('user_id', Auth::id())->get();
        return response()->json($queues);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $queue = Queue::findOrFail($id);


        $queue->status = $request->status;

        if ($request->status === 'accepted') {
            $count = Queue::where('merchant_id', $queue->merchant_id)
                ->where('date', $queue->date)
                ->where('status', 'accepted')
                ->count();
            $queue->queue_number = $count + 1;
        }

        $queue->save();

        return response()->json(['message' => 'Queue status updated', 'data' => $queue]);
    }


   public function merchantQueues()
{
    $user = auth()->user();

    if (!$user->merchant) {
        return response()->json(['error' => 'Merchant not found for this user'], 404);
    }

    $merchantId = $user->merchant->id;

    $queues = Queue::with('user', 'service')
                ->where('merchant_id', $merchantId)
                ->orderBy('date')
                ->orderBy('time')
                ->get();

    return response()->json($queues);
}

 public function update(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update($request->all());

        broadcast(new QueueUpdated($queue))->toOthers();

        return response()->json(['message' => 'Queue updated']);
    }
}
