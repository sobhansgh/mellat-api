<?php

namespace Sobhansgh\MellatApi\Contracts;

interface GatewayContract
{
    /** Initiate a payment and get RefId + redirect URL */
    public function initiate(int $amountToman, ?string $orderId = null, ?string $additionalData = null): array;

    /** Verify payment by returning settled result */
    public function verify(string $orderId, string $saleOrderId, string $saleReferenceId): array;
}
