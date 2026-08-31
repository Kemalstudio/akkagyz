<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\CompareItem;
use App\Models\WishlistItem;
use Illuminate\Support\Collection;

class ShoppingState
{
    private ?Collection $cartQuantities = null;

    private ?Collection $wishlistProductIds = null;

    private ?Collection $compareProductIds = null;

    public function cartQuantity(int $productId): int
    {
        return (int) $this->cartQuantities()->get($productId, 0);
    }

    public function cartCount(): int
    {
        return (int) $this->cartQuantities()->sum();
    }

    public function inWishlist(int $productId): bool
    {
        return $this->wishlistProductIds()->contains($productId);
    }

    public function wishlistCount(): int
    {
        return $this->wishlistProductIds()->count();
    }

    public function inCompare(int $productId): bool
    {
        return $this->compareProductIds()->contains($productId);
    }

    public function compareCount(): int
    {
        return $this->compareProductIds()->count();
    }

    private function cartQuantities(): Collection
    {
        if ($this->cartQuantities !== null) {
            return $this->cartQuantities;
        }

        $query = CartItem::query();
        $user = auth()->user();
        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($token = GuestCart::token(false)) {
            $query->where('guest_token', $token);
        } else {
            return $this->cartQuantities = collect();
        }

        return $this->cartQuantities = $query->pluck('quantity', 'product_id');
    }

    private function wishlistProductIds(): Collection
    {
        if ($this->wishlistProductIds !== null) {
            return $this->wishlistProductIds;
        }

        $userId = auth()->id();

        return $this->wishlistProductIds = $userId
            ? WishlistItem::where('user_id', $userId)->pluck('product_id')
            : collect();
    }

    private function compareProductIds(): Collection
    {
        if ($this->compareProductIds !== null) {
            return $this->compareProductIds;
        }

        $userId = auth()->id();

        return $this->compareProductIds = $userId
            ? CompareItem::where('user_id', $userId)->pluck('product_id')
            : collect();
    }
}
