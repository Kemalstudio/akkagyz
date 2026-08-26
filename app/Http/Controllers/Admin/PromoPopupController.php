<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoPopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoPopupController extends Controller
{
    public function index(Request $request)
    {
        $query=PromoPopup::query()->when($request->filled('search'),fn($q)=>$q->where('title','like','%'.$request->string('search').'%'))->when($request->filled('status'),fn($q)=>$q->where('is_active',$request->string('status')==='active'));
        $stats=['total'=>PromoPopup::count(),'active'=>PromoPopup::active()->count(),'hidden'=>PromoPopup::where('is_active',false)->count()];
        $popups = $query->latest()->paginate(12)->withQueryString();

        return view('admin.popups.index', compact('popups','stats'));
    }

    public function create()
    {
        return view('admin.popups.form', ['popup' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['image_path'] = $request->file('image')->store('popups', 'public');

        if ($data['is_active'] ?? false) {
            PromoPopup::query()->update(['is_active' => false]);
        }

        PromoPopup::create($data);

        return redirect()->route('admin.popups.index')->with('status', 'Попап добавлен.');
    }

    public function edit(PromoPopup $popup)
    {
        return view('admin.popups.form', compact('popup'));
    }

    public function update(Request $request, PromoPopup $popup)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($popup->image_path);
            $data['image_path'] = $request->file('image')->store('popups', 'public');
        }

        if ($data['is_active'] ?? false) {
            PromoPopup::query()->where('id', '!=', $popup->id)->update(['is_active' => false]);
        }

        $popup->update($data);

        return redirect()->route('admin.popups.index')->with('status', 'Попап обновлён.');
    }

    public function destroy(PromoPopup $popup)
    {
        Storage::disk('public')->delete($popup->image_path);
        $popup->delete();

        return back()->with('status', 'Попап удалён.');
    }

    public function toggle(PromoPopup $popup)
    {
        if (! $popup->is_active) {
            PromoPopup::query()->update(['is_active' => false]);
        }

        $popup->update(['is_active' => ! $popup->is_active]);

        return back()->with('status', $popup->is_active ? 'Попап включён.' : 'Попап скрыт.');
    }

    private function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:60'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'max:8192'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
