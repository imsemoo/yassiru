<?php

namespace App\Enums;

enum PaymentType: string
{
    case Contribution = 'contribution';
    case GuaranteeFee = 'guarantee_fee';
    case WeddingRegistration = 'wedding';
}
