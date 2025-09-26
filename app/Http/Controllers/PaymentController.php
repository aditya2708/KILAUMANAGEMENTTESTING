<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Midtrans\Midtrans\Config;
use App\Midtrans\Midtrans\Snap;
use App\Midtrans\Midtrans\CoreApi;
class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        // Konfigurasi Midtrans
        // return $request->all();
        Config::$serverKey = 'SB-Mid-server-2nJfFEgvKuFWIzDFQ9Q7cfEh'; // Ganti dengan server key Anda
        Config::$isProduction = false; // Ubah ke true jika di produksi
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Data transaksi
        $params = [
            'payment_type' => 'gopay',
            'transaction_details' => [
                'order_id' => 'ORDER-' . time(),
                'gross_amount' => $request->gross_amount
            ],
            'gopay' => [
                'enable_callback' => false,
                'callback_url' => '', // Ganti dengan URL callback Anda
            ],
            "additionalInfo" => [
              	"accessToken" => "MjAyMjEwMTM2NjE1OGRiMS00NmM1LTQxMWQtYmU4NC01ODk1ZTdhMjg2NmY6OGNmM2U4NWUtZTc3Mi00NTJmLWFkYmEtNDcyNjRiOWZiZWIw",
              	"merchantId" =>  "G123123",
              	"subMerchantId" =>  "pop-id",
              	"paymentType" =>  "gopay",
              	"accountStatus" => "ENABLED",
              	"statusMessage" => "Account linked"
                ]
        ];

        try {
            $response = CoreApi::charge($request->all());
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
