<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PmOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['super-admin', 'project-manager'])) {
            abort(403, 'Access restricted to Super Admin and Project Manager roles.');
        }
        return $next($request);
    }
}
