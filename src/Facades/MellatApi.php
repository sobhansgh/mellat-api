<?php

namespace Sobhansgh\MellatApi\Facades;

use Illuminate\Support\Facades\Facade;

class MellatApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sobhansgh\MellatApi\MellatClient::class;
    }
}
