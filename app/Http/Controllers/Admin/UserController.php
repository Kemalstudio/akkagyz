<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $base = User::where('role', 'customer');
        $stats=['total'=>(clone $base)->count(),'active'=>(clone $base)->where('is_blocked',false)->count(),'blocked'=>(clone $base)->where('is_blocked',true)->count(),'new'=>(clone $base)->where('created_at','>=',now()->subDays(30))->count()];
        $users = $base->withCount('orders')->withSum('orders','total')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone','like',$term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('is_blocked', $request->string('status') === 'blocked'))
            ->when($request->boolean('new'), fn ($q) => $q->where('created_at', '>=', now()->subDays(30)))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.users', compact('users','stats'));
    }

    public function block(User $user)
    {
        $user->update(['is_blocked' => ! $user->is_blocked]);

        return back()->with('status', $user->is_blocked ? 'Пользователь заблокирован.' : 'Пользователь разблокирован.');
    }
}
