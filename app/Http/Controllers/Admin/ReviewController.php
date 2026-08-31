<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\Review;use App\Models\ReviewReport;use Illuminate\Http\Request;
class ReviewController extends Controller{
 public function index(Request $r){
  $perPage=in_array($r->integer('per_page'),[15,30,50],true)?$r->integer('per_page'):15;
  $sort=in_array($r->input('sort'),['latest','oldest','rating_desc','rating_asc','reports_desc'],true)?$r->input('sort'):'latest';
  $search=mb_substr(trim((string)$r->input('search')),0,120);
  $query=Review::with(['product','user','replies.user'])->withCount('reports');
  $query->when($r->input('status')==='flagged',fn($q)=>$q->whereIn('status',['hidden','rejected']));
  $query->when($r->filled('status')&&$r->input('status')!=='flagged',fn($q)=>$q->where('status',$r->string('status')));
  $query->when($r->filled('rating'),fn($q)=>$q->where('rating',$r->integer('rating')));
  $query->when($r->boolean('reported'),fn($q)=>$q->whereHas('reports',fn($rep)=>$rep->where('status','pending')));
  $query->when($search!=='',function($q)use($search){
   $term='%'.$search.'%';
   $q->where(fn($nested)=>$nested->where('comment','like',$term)
    ->orWhereHas('product',fn($p)=>$p->where('name','like',$term))
    ->orWhereHas('user',fn($u)=>$u->where('name','like',$term)));
  });
  match($sort){
   'oldest'=>$query->oldest(),
   'rating_desc'=>$query->orderByDesc('rating')->orderByDesc('id'),
   'rating_asc'=>$query->orderBy('rating')->orderByDesc('id'),
   'reports_desc'=>$query->orderByDesc('reports_count')->orderByDesc('id'),
   default=>$query->latest(),
  };
  $reviews=$query->paginate($perPage)->withQueryString();
  $overview=[
   'total'=>Review::count(),
   'published'=>Review::where('status','published')->count(),
   'flagged'=>Review::whereIn('status',['hidden','rejected'])->count(),
   'reported'=>ReviewReport::where('status','pending')->count(),
  ];
  return view('admin.reviews.index',compact('reviews','overview'));
 }
 public function moderate(Request $r,Review $review){$d=$r->validate(['status'=>['required','in:published,hidden,rejected'],'moderation_reason'=>['nullable','string','max:1000']]);$review->update($d+['moderated_by'=>$r->user()->id,'moderated_at'=>now()]);$review->reports()->update(['status'=>'reviewed']);$this->rating($review);return back()->with('status','Решение по отзыву сохранено.');}
 private function rating(Review $review):void{$q=$review->product->reviews()->published();$review->product->update(['rating_avg'=>round($q->avg('rating')??0,2),'rating_count'=>$q->count()]);}
}
