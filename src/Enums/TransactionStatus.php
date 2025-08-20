<?php

namespace Sobhansgh\MellatApi\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED  = 'failed';
}
