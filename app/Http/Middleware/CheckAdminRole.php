<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }

        return $next($request);
    }
}
