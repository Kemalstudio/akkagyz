<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Payment;use App\Models\SellerPayout;use App\Models\SellerTransaction;use App\Models\User;use App\Services\MarketplaceFinanceService;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use Illuminate\Support\Str;use Illuminate\Validation\ValidationException;
class FinanceController extends Controller{
 public function index(Request $r){
  $perPage=in_array($r->integer('per_page'),[15,30,50],true)?$r->integer('per_page'):15;
  $sort=in_array($r->input('sort'),['latest','oldest','amount_desc','amount_asc'],true)?$r->input('sort'):'latest';
  $search=mb_substr(trim((string)$r->input('search')),0,120);
  $query=SellerTransaction::with(['seller','orderItem.order']);
  $query->when($r->filled('seller_id'),fn($q)=>$q->where('seller_id',$r->integer('seller_id')));
  $query->when($r->filled('type'),fn($q)=>$q->where('type',$r->string('type')));
  $query->when($r->filled('date_from'),fn($q)=>$q->whereDate('created_at','>=',$r->date('date_from')));
  $query->when($r->filled('date_to'),fn($q)=>$q->whereDate('created_at','<=',$r->date('date_to')));
  $query->when($search!=='',function($q)use($search){
   $term='%'.$search.'%';
   $q->where(fn($nested)=>$nested->where('description','like',$term)->orWhereHas('seller',fn($s)=>$s->where('store_name','like',$term)));
  });
  match($sort){
   'oldest'=>$query->oldest(),
   'amount_desc'=>$query->orderByDesc('net_amount')->orderByDesc('id'),
   'amount_asc'=>$query->orderBy('net_amount')->orderByDesc('id'),
   default=>$query->latest(),
  };
  $transactions=$query->paginate($perPage)->withQueryString();
  $stats=['gross'=>SellerTransaction::where('type','sale')->sum('gross_amount'),'commission'=>SellerTransaction::sum('commission_amount'),'sellerNet'=>SellerTransaction::sum('net_amount'),'pendingPayouts'=>SellerPayout::where('status','pending')->sum('amount'),'refunded'=>SellerTransaction::where('type','refund')->sum('net_amount')];
  $sellers=User::approvedSellers()->orderBy('store_name')->get(['id','store_name']);
  return view('admin.finance.index',compact('transactions','stats','sellers'));
 }
 public function payments(Request $r){
  $perPage=in_array($r->integer('per_page'),[15,30,50],true)?$r->integer('per_page'):15;
  $sort=in_array($r->input('sort'),['latest','oldest','amount_desc','amount_asc'],true)?$r->input('sort'):'latest';
  $query=Payment::with('order.user');
  $query->when($r->filled('status'),fn($q)=>$q->where('status',$r->string('status')));
  $query->when($r->filled('type'),fn($q)=>$q->where('type',$r->string('type')));
  $query->when($r->filled('search'),fn($q)=>$q->whereHas('order',fn($o)=>$o->where('number','like','%'.$r->string('search').'%')));
  $query->when($r->filled('date_from'),fn($q)=>$q->whereDate('created_at','>=',$r->date('date_from')));
  $query->when($r->filled('date_to'),fn($q)=>$q->whereDate('created_at','<=',$r->date('date_to')));
  match($sort){
   'oldest'=>$query->oldest(),
   'amount_desc'=>$query->orderByDesc('amount')->orderByDesc('id'),
   'amount_asc'=>$query->orderBy('amount')->orderByDesc('id'),
   default=>$query->latest(),
  };
  $payments=$query->paginate($perPage)->withQueryString();
  $overview=[
   'total'=>Payment::count(),
   'succeeded'=>(int)Payment::where('status','succeeded')->sum('amount'),
   'pending'=>Payment::where('status','pending')->count(),
   'failed'=>Payment::where('status','failed')->count(),
   'refunded'=>(int)Payment::where('status','refunded')->sum('amount'),
  ];
  return view('admin.finance.payments',compact('payments','overview'));
 }
 public function payouts(Request $r,MarketplaceFinanceService $finance){
  $perPage=in_array($r->integer('per_page'),[15,30,50],true)?$r->integer('per_page'):15;
  $sort=in_array($r->input('sort'),['latest','oldest','amount_desc','amount_asc'],true)?$r->input('sort'):'latest';
  $query=SellerPayout::with(['seller','processor']);
  $query->when($r->filled('seller_id'),fn($q)=>$q->where('seller_id',$r->integer('seller_id')));
  $query->when($r->filled('status'),fn($q)=>$q->where('status',$r->string('status')));
  match($sort){
   'oldest'=>$query->oldest(),
   'amount_desc'=>$query->orderByDesc('amount')->orderByDesc('id'),
   'amount_asc'=>$query->orderBy('amount')->orderByDesc('id'),
   default=>$query->latest(),
  };
  $sellers=User::approvedSellers()->orderBy('store_name')->get();
  $sellerBalances=$sellers->mapWithKeys(fn($s)=>[$s->id=>$finance->balance($s->id)]);
  $overview=[
   'pendingCount'=>SellerPayout::where('status','pending')->count(),
   'pendingAmount'=>(int)SellerPayout::where('status','pending')->sum('amount'),
   'paidCount'=>SellerPayout::where('status','paid')->count(),
   'paidAmount'=>(int)SellerPayout::where('status','paid')->sum('amount'),
  ];
  return view('admin.finance.payouts',[
   'payouts'=>$query->paginate($perPage)->withQueryString(),
   'sellers'=>$sellers,
   'sellerBalances'=>$sellerBalances,
   'overview'=>$overview,
  ]);
 }
 public function storePayout(Request $r,MarketplaceFinanceService $finance){
  $d=$r->validate(['seller_id'=>['required','exists:users,id'],'amount'=>['required','integer','min:1'],'method'=>['required','in:bank_transfer,cash,card'],'note'=>['nullable','string','max:1000']]);
  try{
   DB::transaction(function()use($d,$finance){
    if($d['amount']>$finance->balance((int)$d['seller_id'])){throw ValidationException::withMessages(['amount'=>'Сумма превышает доступный баланс продавца.']);}
    SellerPayout::create($d+['number'=>'PAY-'.strtoupper(Str::random(8))]);
   });
  }catch(ValidationException $e){return back()->withErrors($e->errors())->withInput();}
  return back()->with('status','Выплата создана.');
 }
 public function completePayout(Request $r,SellerPayout $payout,MarketplaceFinanceService $finance){
  abort_unless($payout->status==='pending',422);
  $d=$r->validate(['reference'=>['nullable','string','max:190']]);
  try{
   DB::transaction(function()use($d,$r,$payout,$finance){
    if($payout->amount>$finance->balance($payout->seller_id)){throw ValidationException::withMessages(['amount'=>'Недостаточно средств на балансе продавца.']);}
    SellerTransaction::create(['seller_id'=>$payout->seller_id,'type'=>'payout','net_amount'=>-$payout->amount,'status'=>'available','description'=>'Выплата '.$payout->number]);
    $payout->update(['status'=>'paid','processed_by'=>$r->user()->id,'processed_at'=>now(),'reference'=>$d['reference']??null]);
   });
  }catch(ValidationException $e){return back()->withErrors($e->errors());}
  return back()->with('status','Выплата отмечена оплаченной.');
 }
}
