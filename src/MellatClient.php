<?php

namespace Sobhansgh\MellatApi;

use SoapClient;
use Illuminate\Support\Str;
use Sobhansgh\MellatApi\Contracts\GatewayContract;
use Sobhansgh\MellatApi\Exceptions\GatewayException;
use Sobhansgh\MellatApi\Models\MellatApiLog;
use Sobhansgh\MellatApi\Enums\TransactionStatus;

class MellatClient implements GatewayContract
{
    public function __construct(
        protected string $terminalId,
        protected string $username,
        protected string $password,
        protected string $wsdl,
        protected bool $convertToRial,
        protected string $callbackUrl
    ) {}

    protected function client(): SoapClient
    {
        return new SoapClient($this->wsdl, ['encoding' => 'UTF-8']);
    }

    /**
     * Initiate payment. Returns array: [ref_id, redirect_url, order_id]
     */
    public function initiate(int $amountToman, ?string $orderId = null, ?string $additionalData = null): array
    {
        $orderId = $orderId ?: (string) Str::ulid();
        $amount  = $this->convertToRial ? rial($amountToman) : $amountToman;

        $params = [
            'terminalId'    => $this->terminalId,
            'userName'      => $this->username,
            'userPassword'  => $this->password,
            'orderId'       => $orderId,
            'amount'        => $amount,
            'localDate'     => now()->format('Ymd'),
            'localTime'     => now()->format('His'),
            'additionalData'=> $additionalData ?? '',
            'callBackUrl'   => $this->callbackUrl,
            'payerId'       => 0,
        ];

        $log = MellatApiLog::create([
            'order_id' => $orderId,
            'amount'   => $amountToman,
            'status'   => TransactionStatus::PENDING->value,
            'meta'     => ['request' => $params],
        ]);

        try {
            $response = $this->client()->bpPayRequest($params);
            $result = explode(",", $response->return ?? '');
            $resCode = $result[0] ?? null;
            $refId   = $result[1] ?? null;

            $log->update(['res_code' => $resCode, 'ref_id' => $refId]);

            if ($resCode !== '0' || empty($refId)) {
                throw new GatewayException('Mellat bpPayRequest failed: '.$resCode);
            }

            $redirectUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat?RefId='.$refId;

            return [
                'ok' => true,
                'ref_id' => $refId,
                'redirect_url' => $redirectUrl,
                'order_id' => $orderId,
            ];
        } catch (\Throwable $e) {
            $log->update(['status' => TransactionStatus::FAILED->value, 'meta' => ['error' => $e->getMessage()]]);
            throw new GatewayException($e->getMessage(), previous: $e);
        }
    }

    /**
     * Verify + settle. Return JSON-like array.
     */
    public function verify(string $orderId, string $saleOrderId, string $saleReferenceId): array
    {
        $log = MellatApiLog::where('order_id', $orderId)->firstOrFail();

        $creds = [
            'terminalId'   => $this->terminalId,
            'userName'     => $this->username,
            'userPassword' => $this->password,
        ];

        try {
            $verifyParams = $creds + [
                'orderId'        => $orderId,
                'saleOrderId'    => $saleOrderId,
                'saleReferenceId'=> $saleReferenceId,
            ];
            $verifyRes = $this->client()->bpVerifyRequest($verifyParams);
            $verifyCode = (string)($verifyRes->return ?? '');

            if ($verifyCode !== '0') {
                $log->update(['status' => TransactionStatus::FAILED->value, 'res_code' => $verifyCode]);
                return [
                    'ok' => false,
                    'status' => TransactionStatus::FAILED->value,
                    'res_code' => $verifyCode,
                ];
            }

            $settleRes = $this->client()->bpSettleRequest($verifyParams);
            $settleCode = (string)($settleRes->return ?? '');

            $log->update([
                'status' => TransactionStatus::SUCCESS->value,
                'sale_order_id' => $saleOrderId,
                'sale_reference_id' => $saleReferenceId,
                'res_code' => $settleCode,
            ]);

            return [
                'ok' => true,
                'status' => TransactionStatus::SUCCESS->value,
                'res_code' => $settleCode,
                'order_id' => $orderId,
                'sale_order_id' => $saleOrderId,
                'sale_reference_id' => $saleReferenceId,
            ];
        } catch (\Throwable $e) {
            $log->update(['status' => TransactionStatus::FAILED->value, 'meta' => ['error' => $e->getMessage()]]);
            throw new GatewayException($e->getMessage(), previous: $e);
        }
    }
}
