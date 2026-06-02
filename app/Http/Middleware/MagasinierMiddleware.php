<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MagasinierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['access' => 'Acces reserve aux magasiniers.']);
        }

        $user = Auth::user();
        $isMagasinierRole = $user->role === 'magasinier';
        $isEmployeeMagasinier = $user->role === 'employee' && $user->position === 'magasinier';

        if (!$isMagasinierRole && !$isEmployeeMagasinier) {
            return redirect()->route('login')
                ->withErrors(['access' => 'Acces reserve aux magasiniers.']);
        }

        return $next($request);
    }
}
