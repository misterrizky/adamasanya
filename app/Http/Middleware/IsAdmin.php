<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if (!$user || !in_array($user->getRoleNames()[0] ?? '', ['Super Admin', 'Owner', 'Cabang', 'Pegawai'])) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
