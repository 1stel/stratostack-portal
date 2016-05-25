<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class VerifyUserStatus
{
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (null !== $this->auth->user() && !$this->auth->guest())
        {
            // Check to see if user is active
            if ($this->auth->user()->verified == 1 || $request->is('emailVerification/*'))
                return $next($request);
            else
            {
                if (!$request->is('auth/validation-needed'))
                    return redirect('/auth/validation-needed');
            }

        }

        return $next($request);
    }
}
