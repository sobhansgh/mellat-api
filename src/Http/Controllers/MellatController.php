<?php

namespace Sobhansgh\MellatApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sobhansgh\MellatApi\MellatClient;
use Sobhansgh\MellatApi\Models\MellatApiLog;

class MellatController extends Controller
{
    public function __construct(protected MellatClient $mellat) {}

    /** POST /pay  {amount: int (toman), order_id?: string, additional?: string} */
    public function pay(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1000', // toman
            'order_id' => 'nullable|string|max:64',
            'additional' => 'nullable|string|max:255',
        ]);

        $data = $this->mellat->initiate($validated['amount'], $validated['order_id'] ?? null, $validated['additional'] ?? null);

        return response()->json($data);
    }

    /** POST /verify  {order_id, sale_order_id, sale_reference_id} */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'sale_order_id' => 'required|string',
            'sale_reference_id' => 'required|string',
        ]);

        $result = $this->mellat->verify($validated['order_id'], $validated['sale_order_id'], $validated['sale_reference_id']);

        return response()->json($result);
    }

    /** GET/POST /callback */
    public function callback(Request $request)
    {
        $payload = [
            'ResCode' => $request->input('ResCode'),
            'SaleOrderId' => $request->input('SaleOrderId'),
            'SaleReferenceId' => $request->input('SaleReferenceId'),
            'OrderId' => $request->input('OrderId') ?? $request->input('SaleOrderId'),
            'RefId' => $request->input('RefId'),
        ];

        if (!empty($payload['OrderId'])) {
            MellatApiLog::where('order_id', $payload['OrderId'])->update([
                'res_code' => (string) ($payload['ResCode'] ?? ''),
            ]);
        }

        return response()->json([
            'ok' => ((string)($payload['ResCode'] ?? '')) === '0',
            'message' => ((string)($payload['ResCode'] ?? '')) === '0' ? 'OK' : 'FAILED',
            'data' => $payload,
            'next' => 'POST /api/payments/mellat-api/verify with order_id, sale_order_id, sale_reference_id',
        ]);
    }
}
