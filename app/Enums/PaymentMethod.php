<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card = 'card';
    case FawryRef = 'fawry_ref';
    case Wallet = 'wallet';
}
