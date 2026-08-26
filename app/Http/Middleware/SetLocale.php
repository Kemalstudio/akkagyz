<?php
namespace App\Http\Middleware;use App\Models\BusinessSetting;use Closure;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\Response;
class SetLocale{public function handle(Request $request,Closure $next):Response{$settings=BusinessSetting::current();$enabled=$settings->enabled_locales?:['ru','tk','en'];$fallback=in_array($settings->default_locale,$enabled,true)?$settings->default_locale:'ru';$locale=$request->session()->get('locale',$fallback);app()->setLocale(in_array($locale,$enabled,true)?$locale:$fallback);return $next($request);}}
