<?php
namespace App\Http\Middleware;
use Closure;
class Cors
{
	/**

	*/
	public function handle( $request, Closure $next)
	{

		$response = $next(
			$request
        );
        $response->headers->set(
			'Access-Control-Allow-Origin',
			'*'
        );
        $response->headers->set(
			'Access-Control-Allow-Methods',
			'GET, POST, PUT, DELETE, PATH, OPTIONS'
        );
        $response->headers->set(
			'Access-Control-Allow-Headers',
			'Content-type, Authorization'
        );

        $response->headers->set(
            'Access-Control-Allow-Credentials',
            'true'
        );

        return $response;
	}
}
?>
