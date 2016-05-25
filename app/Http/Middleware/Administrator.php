<?php namespace App\Http\Middleware;

use Closure;

class Administrator {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        if ($request->user())
        {
            if ('Admin' != $request->user()->access)
            {
                return redirect('/');
            }
        }
        else
        {
            return redirect('/');
        }

		return $next($request);
	}

}
