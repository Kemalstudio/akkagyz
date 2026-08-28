<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->integer('per_page'), [20, 50, 100], true)
            ? $request->integer('per_page')
            : 20;
        $level = in_array($request->input('level'), ['top', 'sub'], true)
            ? $request->input('level')
            : null;

        $overview = [
            'total' => Category::count(),
            'top' => Category::whereNull('parent_id')->count(),
            'sub' => Category::whereNotNull('parent_id')->count(),
            'empty' => Category::doesntHave('products')->count(),
        ];

        $categories = Category::with('parent')->withCount(['products', 'children'])
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->integer('parent_id') > 0, fn ($q) => $q->where('parent_id', $request->integer('parent_id')))
            ->when($level === 'top', fn ($q) => $q->whereNull('parent_id'))
            ->when($level === 'sub', fn ($q) => $q->whereNotNull('parent_id'))
            ->when($request->boolean('empty'), fn ($q) => $q->doesntHave('products'))
            ->orderBy('sort_order')->orderBy('name')->paginate($perPage)->withQueryString();
        $parents = Category::topLevel()->orderBy('name')->get(['id', 'name']);

        return view('admin.categories.index', compact('categories', 'overview', 'parents'));
    }
    public function create() { return view('admin.categories.form', ['category'=>null, 'parents'=>$this->parentOptions()]); }
    public function store(Request $request)
    {
        $data=$this->validated($request); $data['slug']=$this->uniqueSlug($data['name']); Category::create($data);
        return redirect()->route('admin.categories.index')->with('status','Категория создана.');
    }
    public function edit(Category $category)
    {
        return view('admin.categories.form', ['category'=>$category,'parents'=>$this->parentOptions($category)]);
    }
    public function update(Request $request, Category $category)
    {
        $data=$this->validated($request,$category); if($category->name!==$data['name']) $data['slug']=$this->uniqueSlug($data['name'],$category); $category->update($data);
        return redirect()->route('admin.categories.index')->with('status','Категория обновлена.');
    }
    public function destroy(Category $category)
    {
        if($category->children()->exists()||$category->products()->exists()) return back()->withErrors(['category'=>'Нельзя удалить категорию с подкатегориями или товарами.']);
        $category->delete(); return back()->with('status','Категория удалена.');
    }
    private function validated(Request $request, ?Category $category=null): array
    {
        $forbiddenParentIds = $category ? [$category->id, ...$category->descendantIds()] : [];

        return $request->validate(['name'=>['required','string','max:120'],'parent_id'=>['nullable','exists:categories,id',Rule::notIn($forbiddenParentIds)],'icon'=>['nullable','string','max:60'],'sort_order'=>['required','integer','min:0','max:9999']]);
    }

    /**
     * Every category, indented by depth, for the parent-category dropdown.
     * Editing a category excludes itself and its descendants to prevent a
     * cycle (a category cannot become its own ancestor).
     */
    private function parentOptions(?Category $exclude = null): \Illuminate\Support\Collection
    {
        $forbiddenIds = $exclude ? [$exclude->id, ...$exclude->descendantIds()] : [];
        $byParent = Category::orderBy('sort_order')->orderBy('name')->get(['id', 'parent_id', 'name'])->groupBy('parent_id');

        $options = collect();
        $walk = function ($parentId, int $depth) use (&$walk, &$options, $byParent, $forbiddenIds) {
            foreach ($byParent->get($parentId, collect()) as $node) {
                if (in_array($node->id, $forbiddenIds, true)) {
                    continue;
                }
                $options->push((object) ['id' => $node->id, 'label' => str_repeat('— ', $depth).$node->name]);
                $walk($node->id, $depth + 1);
            }
        };
        $walk(null, 0);

        return $options;
    }
    private function uniqueSlug(string $name, ?Category $ignore=null): string
    {
        $base=Str::slug($name)?:'category'; $slug=$base; $index=2;
        while(Category::where('slug',$slug)->when($ignore,fn($q)=>$q->whereKeyNot($ignore->id))->exists()) $slug=$base.'-'.$index++;
        return $slug;
    }
}
