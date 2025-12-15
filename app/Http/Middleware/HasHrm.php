<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasHrm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = getParentUser();

        if ($user->hrm_access == Status::NO) {
            abort(403, "Your current subscription plan does not include access to the HRM module. Please consider upgrading to a plan that includes this feature.");
        }

        return $next($request);
    }
}
