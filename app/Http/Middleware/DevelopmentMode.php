<?php
namespace App\Http\Middleware;
use App\Models\BusinessSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class DevelopmentMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->is('admin','admin/*','login','logout','otp','otp/*','up','storage/*') || $request->user()?->isAdmin()) return $next($request);
        $settings=BusinessSetting::current();
        if($settings->development_mode) return response()->view('maintenance.business',['settings'=>$settings],status:503);
        return $next($request);
    }
}
