<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CashRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cashRegister = getActiveCashRegister();

        if (!$cashRegister) {

            if (isApiRequest()) {
                return jsonResponse("error", "error", ["You must have an active cash register to perform this action. Please create a cash register from the web"]);
            }

            return to_route('user.cash_register.dashboard');
        }

        return $next($request);
    }
}
