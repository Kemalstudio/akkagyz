<?php
namespace App\Http\Middleware;
use Closure;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\Response;
class TranslateInterface{
 public function handle(Request $request,Closure $next):Response{
  $response=$next($request);$locale=app()->getLocale();
  if($locale==='ru'||!method_exists($response,'getContent')||!str_contains((string)$response->headers->get('Content-Type'),'text/html'))return $response;
  $map=array_merge(trans('site',[],$locale),trans('extended',[],$locale));if(!is_array($map))return $response;
  uksort($map,fn($a,$b)=>mb_strlen($b)<=>mb_strlen($a));$html=$response->getContent();
  $html=str_replace('<html lang="ru">','<html lang="'.$locale.'">',$html);$response->setContent(strtr($html,$map));return $response;
 }
}
