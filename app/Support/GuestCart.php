<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Identifies a guest shopper's cart via a random token kept in their
 * session, so ordering works without an account while wishlist/compare
 * stay account-only.
 */
class GuestCart
{
    /** Get the current guest cart token, creating one only if $create is true. */
    public static function token(bool $create = true): ?string
    {
        if (! session()->has('guest_cart_token')) {
            if (! $create) {
                return null;
            }
            session(['guest_cart_token' => Str::random(40)]);
        }

        return session('guest_cart_token');
    }

    /** Scope a cart_items query to either the authenticated user or the guest token. */
    public static function scope($query, ?User $user)
    {
        return $user
            ? $query->where('user_id', $user->id)
            : $query->where('guest_token', self::token());
    }

    /** Move a just-logged-in/registered guest's cart into their account, merging quantities. */
    public static function mergeInto(User $user): void
    {
        $token = self::token(false);
        if (! $token) {
            return;
        }

        CartItem::where('guest_token', $token)->get()->each(function (CartItem $guestItem) use ($user) {
            $existing = $user->cartItems()->where('product_id', $guestItem->product_id)->first();
            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['user_id' => $user->id, 'guest_token' => null]);
            }
        });

        session()->forget('guest_cart_token');
    }
}
