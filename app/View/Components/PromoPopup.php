<?php

namespace App\View\Components;

use App\Models\PromoPopup as PromoPopupModel;
use Illuminate\View\Component;
use Illuminate\View\View;

class PromoPopup extends Component
{
    public function render(): View
    {
        return view('components.promo-popup', [
            'popup' => PromoPopupModel::active()->latest()->first(),
        ]);
    }
}
