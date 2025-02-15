<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('doctor')->check()) {
            return $next($request);
        }  
          else{

              return response()->json([
                  'success' => false,
                  'message' => 'Unauthorized. Only doctors can access this route.',
              ], 403);
          }
    }
}
