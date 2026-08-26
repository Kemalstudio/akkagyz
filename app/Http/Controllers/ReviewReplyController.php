<?php
namespace App\Http\Controllers;
use App\Models\Review; use Illuminate\Http\Request;
class ReviewReplyController extends Controller
{
 public function store(Request $request, Review $review){$data=$request->validate(['message'=>['required','string','max:1500']]);$review->replies()->create(['user_id'=>$request->user()->id,'message'=>$data['message']]);return back()->with('status','Ответ опубликован.');}
 public function destroy(Request $request, \App\Models\ReviewReply $reply){abort_unless($request->user()->isAdmin()||$reply->user_id===$request->user()->id,403);$reply->delete();return back()->with('status','Ответ удалён.');}
}
