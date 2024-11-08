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
            $currentPath = $request->path();

            // Tentukan path yang sesuai untuk setiap role
            $rolePaths = [
                'admin' => 'admin',
                'cashier' => 'cashier',
                'owner' => 'owner',
            ];

            // Periksa apakah path yang diakses sesuai dengan role
            if (isset($rolePaths[$role]) && !str_starts_with($currentPath, $rolePaths[$role])) {
                // Redirect ke dashboard sesuai role jika path tidak sesuai
                switch ($role) {
                    case 'admin':
                        return redirect()->route('filament.admin.pages.dashboard');
                    case 'cashier':
                        return redirect()->route('filament.cashier.pages.dashboard');
                    case 'owner':
                        return redirect()->route('filament.owner.pages.dashboard');
                }
            }
        }

        // Jika path sesuai atau pengguna tidak terautentikasi, lanjutkan
        return $next($request);
    }

}
