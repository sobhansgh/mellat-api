<?php

namespace Sobhansgh\MellatApi\Models;

use Illuminate\Database\Eloquent\Model;
use Sobhansgh\MellatApi\Enums\TransactionStatus;

class MellatApiLog extends Model
{
    protected $table = 'mellat_api_logs';
    protected $guarded = [];
    protected $casts = [
        'meta' => 'array',
    ];

    public function scopeSuccessful($q) { return $q->where('status', TransactionStatus::SUCCESS->value); }
    public function scopeUnsuccessful($q) { return $q->where('status', TransactionStatus::FAILED->value); }
    public function scopePending($q) { return $q->where('status', TransactionStatus::PENDING->value); }
}
