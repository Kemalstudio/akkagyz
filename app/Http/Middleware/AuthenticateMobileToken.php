<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->bearerToken();
        $token = $plain ? MobileApiToken::with('user')->where('token_hash', hash('sha256', $plain))->first() : null;

        if (! $token || ($token->expires_at && $token->expires_at->isPast()) || ! $token->user || $token->user->is_blocked) {
            return response()->json(['message' => 'Необходима авторизация.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        $request->setUserResolver(fn () => $token->user);
        $request->attributes->set('mobile_token', $token);

        return $next($request);
    }
}
