<?php

namespace App\Http\Middleware;

use App\Models\AccountDeal;
use App\Providers\RouteServiceProvider;
use App\Services\ZohoApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ZohoValidAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $zohApi = new ZohoApi();
            $account = $zohApi->findById('Accounts', 1, ['Account_Name']);
            if (isset($account->status) && $account->status == 'error' && isset($account->code) && $account->code == "INVALID_TOKEN") {
                $zohApi = new ZohoApi();
                $zohApi->generatetNewAccessToken();
            }
        }
        return $next($request);
    }
}
