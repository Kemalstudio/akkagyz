<?php

namespace App\Contracts;

use App\Models\Order;

/**
 * Extension point for online payment processing. Register implementations
 * in AppServiceProvider's PaymentGatewayResolver binding — nothing else in
 * the checkout flow needs to change when a real provider is added.
 */
interface PaymentGateway
{
    /**
     * Attempt to charge for the order. Implementations that can't charge
     * synchronously (redirect-based providers) may leave payment_status as
     * "pending" and settle it later via a webhook.
     */
    public function charge(Order $order): void;
}
