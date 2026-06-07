<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\ApiResponse;

class CheckRole
{
  use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
      if (!$request->user()) {
        return $this->errorResponse('Unauthorized', 401);
      }

      if ($request->user()->role !== $role){
        return $this->errorResponse('Forbidden', 403);
      }

      return $next($request);
    }
}
