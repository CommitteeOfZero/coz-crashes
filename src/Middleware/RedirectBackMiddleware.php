<?php namespace CoZCrashes\Middleware;

// On success, redirect back if possible, or serve blank page if no referer

class RedirectBackMiddleware extends \CoZCrashes\Base {
    public function __invoke($request, $response, $next) {
        $response = $next($request, $response);
        if ($response->getStatusCode() != 200) return $response;
        if ($request->hasHeader('Referer')) return $response->withRedirect($request->getHeader('Referer')[0]);
        return $response->withStatus(200);
    }
}