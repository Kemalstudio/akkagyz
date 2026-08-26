<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\Order;

/**
 * Used for "cash on delivery" and for any method offered in a client
 * (web/mobile) that isn't backed by a real processor yet. No online charge
 * happens — the order simply stays "unpaid" until staff confirm receipt.
 */
class ManualPaymentGateway implements PaymentGateway
{
    public function charge(Order $order): void
    {
        // Intentionally a no-op: payment is settled in person (cash) or the
        // method isn't wired to a processor yet.
    }
}
