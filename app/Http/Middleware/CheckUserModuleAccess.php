<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $moduleRoleId)
    {
        $userRole = Auth::user()->role_id;

        // Admin (role_id = 1) can access all index methods, but only Teacher CRUD for create/edit/delete
        // Teacher (role_id = 2) can only access Student/Parent module
        if ($userRole == 2 && $moduleRoleId == 2) {
            // Teacher trying to access Teacher module
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}
