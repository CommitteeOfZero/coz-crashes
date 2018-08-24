<?php namespace CoZCrashes\Middleware;

class AdminAreaMiddleware extends \CoZCrashes\Base {
    public function __invoke($request, $response, $next) {
        $request = $request->withAttribute('isAdminArea', true);
        return $next($request, $response);
    }
}