<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            // Cek apakah pengguna sudah berada di dashboard yang sesuai
            if ($role === 'admin' && !$request->routeIs('filament.admin.pages.dashboard')) {
                return redirect()->route('filament.admin.pages.dashboard');
            } elseif ($role === 'cashier' && !$request->routeIs('filament.cashier.pages.dashboard')) {
                return redirect()->route('filament.cashier.pages.dashboard');
            } elseif ($role === 'owner' && !$request->routeIs('filament.owner.pages.dashboard')) {
                return redirect()->route('filament.owner.pages.dashboard');
            }
        }

        return $next($request);
    }

}
