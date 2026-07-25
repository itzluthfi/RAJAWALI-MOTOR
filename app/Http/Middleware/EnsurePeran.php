<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePeran
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        $peranPengguna = $request->user()?->peran;

        if (! $peranPengguna || ! in_array($peranPengguna, $peranDiizinkan, true)) {
            abort(403, 'Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
