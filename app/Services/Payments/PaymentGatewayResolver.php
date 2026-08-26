<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use Illuminate\Validation\ValidationException;

class PaymentGatewayResolver
{
    /** @param array<string, PaymentGateway> $gateways */
    public function __construct(private array $gateways) {}

    public function resolve(string $method): PaymentGateway
    {
        return $this->gateways[$method]
            ?? throw ValidationException::withMessages(['payment_method' => 'Этот способ оплаты сейчас недоступен.']);
    }
}
