<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\AuditLog;use App\Models\User;use Illuminate\Http\Request;use Illuminate\Support\Facades\Hash;use Illuminate\Validation\Rule;
class AdminAccessController extends Controller{public const PERMISSIONS=['dashboard','orders','catalog','moderation','finance','marketing','support','system'];public function admins(){return view('admin.access.admins',['admins'=>User::where('role','admin')->latest()->get(),'permissions'=>self::PERMISSIONS]);}public function store(Request $r){$d=$r->validate(['name'=>['required','string','max:120'],'email'=>['required','email','unique:users,email'],'password'=>['required','string','min:8'],'admin_role'=>['required',Rule::in(['super_admin','order_manager','content_manager','moderator','support','accountant','marketer'])],'permissions'=>['nullable','array'],'permissions.*'=>[Rule::in(self::PERMISSIONS)]]);$d['password']=Hash::make($d['password']);$d['role']='admin';$d['admin_permissions']=$d['admin_role']==='super_admin'?self::PERMISSIONS:($d['permissions']??[]);unset($d['permissions']);User::create($d);return back()->with('status','Администратор создан.');}public function update(Request $r,User $admin){abort_unless($admin->isAdmin(),404);abort_if($admin->id===$r->user()->id&&$r->input('admin_role')!=='super_admin',422,'Нельзя понизить собственную роль.');$d=$r->validate(['admin_role'=>['required',Rule::in(['super_admin','order_manager','content_manager','moderator','support','accountant','marketer'])],'permissions'=>['nullable','array'],'permissions.*'=>[Rule::in(self::PERMISSIONS)]]);$admin->update(['admin_role'=>$d['admin_role'],'admin_permissions'=>$d['admin_role']==='super_admin'?self::PERMISSIONS:($d['permissions']??[])]);return back()->with('status','Права обновлены.');}
 public function audit(Request $r){
  $perPage=in_array($r->integer('per_page'),[30,50,100],true)?$r->integer('per_page'):30;
  $sort=$r->input('sort')==='oldest'?'oldest':'latest';
  $search=mb_substr(trim((string)$r->input('search')),0,120);
  $query=AuditLog::with('user');
  $query->when($r->filled('user_id'),fn($q)=>$q->where('user_id',$r->integer('user_id')));
  $query->when($r->filled('method'),fn($q)=>$q->where('method',$r->string('method')));
  $query->when($r->boolean('errors_only'),fn($q)=>$q->where('response_status','>=',400));
  $query->when($r->filled('date_from'),fn($q)=>$q->whereDate('created_at','>=',$r->date('date_from')));
  $query->when($r->filled('date_to'),fn($q)=>$q->whereDate('created_at','<=',$r->date('date_to')));
  $query->when($search!=='',function($q)use($search){
   $term='%'.$search.'%';
   $q->where(fn($nested)=>$nested->where('action','like',$term)->orWhere('route','like',$term)->orWhere('ip_address','like',$term));
  });
  $sort==='oldest'?$query->oldest():$query->latest();
  $logs=$query->paginate($perPage)->withQueryString();
  $overview=[
   'total'=>AuditLog::count(),
   'today'=>AuditLog::whereDate('created_at',today())->count(),
   'admins'=>AuditLog::distinct('user_id')->whereNotNull('user_id')->count('user_id'),
   'errors'=>AuditLog::where('response_status','>=',400)->count(),
  ];
  $admins=User::where('role','admin')->orderBy('name')->get(['id','name']);
  return view('admin.access.audit',compact('logs','overview','admins'));
 }
}
