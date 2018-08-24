<?php namespace CoZCrashes\Middleware;

// On success, redirect home

class RedirectHomeMiddleware extends \CoZCrashes\Base {
    public function __invoke($request, $response, $next) {
        $response = $next($request, $response);
        if ($response->getStatusCode() != 200) return $response;

        $isAdminArea = $request->getAttribute('isAdminArea');
        if (!is_null($isAdminArea) && $isAdminArea) {
            return $response->withRedirect($this->c->router->pathFor('adminHome'));
        } else {
            return $response->withRedirect($this->c->router->pathFor('home'));
        }
    }
}