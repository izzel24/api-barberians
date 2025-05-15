<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Illuminate\Http\Request;
use Midtrans\Snap;

class MidtransPaymentController extends Controller
{
    public function getSnapToken(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                    'order_id' => $request->transaction_details['order_id'],
                    'gross_amount' => (int) $request->transaction_details['gross_amount'], 
            ]
            // 'customer_details' => [
            //     'first_name' => $request->name,
            //     'email' => $request->email,
            // ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json(['snap_token' => $snapToken]);
    }

    public function midtransCallback(Request $request)
{
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    $notif = new \Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $fraud = $notif->fraud_status;
    $orderId = explode('-', $notif->order_id)[0]; // ambil ID queue

    $queue = \App\Models\Queue::find($orderId);

    if (!$queue) {
        return response()->json(['message' => 'Queue not found'], 404);
    }

    if ($transaction == 'capture') {
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                $queue->status = 'pending';
            } else {
                $queue->status = 'paid';
            }
        }
    } elseif ($transaction == 'settlement') {
        $queue->status = 'paid';
    } elseif ($transaction == 'pending') {
        $queue->status = 'pending';
    } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
        $queue->status = 'failed';
    }

    $queue->save();

    return response()->json(['message' => 'Callback processed']);
}
}
