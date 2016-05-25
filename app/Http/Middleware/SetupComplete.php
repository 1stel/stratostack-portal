<?php namespace App\Http\Middleware;

use App\SiteConfig;
use Closure;

class SetupComplete {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $setupComplete = SiteConfig::whereParameter('setupComplete')->first();

        if ($setupComplete->data == 'false')
        {
            return redirect('/admin/setup');
        }

		return $next($request);
	}

}
