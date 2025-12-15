<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasStaffPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();

        if ($user->is_staff && !$user->hasStaffPermission($permission)) {
            if (isApiRequest()) {
                $notify = "You do not have permission to access this resource.";
                return responseManager("unauthorized", $notify, "error");
            }
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
