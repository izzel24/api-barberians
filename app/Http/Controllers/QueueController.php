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
            'payment_method' => 'required|in:cash,transfer,midtrans', // tambahkan validasi
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

    // broadcast(new QueueUpdated($queue))->toOthers();
    // broadcast(new QueueUpdated(($queue))->toOthers());
}

    // 2. Get user's queues
    public function userQueues()
    {
        $queues = Queue::with('merchant', 'service')->where('user_id', Auth::id())->get();
        return response()->json($queues);
    }

    // 3. Merchant updates queue status (accept/reject)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $queue = Queue::findOrFail($id);

        // Optional: validasi apakah merchant yang sedang login adalah pemilik antrean ini
        // if ($queue->merchant->user_id !== Auth::id()) return response()->json(['error' => 'Unauthorized'], 403);

        $queue->status = $request->status;

        if ($request->status === 'accepted') {
            // Hitung queue_number berdasarkan antrean accepted sebelumnya
            $count = Queue::where('merchant_id', $queue->merchant_id)
                ->where('date', $queue->date)
                ->where('status', 'accepted')
                ->count();
            $queue->queue_number = $count + 1;
        }

        $queue->save();

        return response()->json(['message' => 'Queue status updated', 'data' => $queue]);
    }

    // 4. Optional: Merchant fetch all queues
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

        // Broadcast event ke frontend
        broadcast(new QueueUpdated($queue))->toOthers();

        return response()->json(['message' => 'Queue updated']);
    }
}
