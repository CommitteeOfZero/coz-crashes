<?php namespace CoZCrashes\Middleware;

class UnauthMiddleware extends \CoZCrashes\Base {
    public function __invoke($request, $response, $next) {
        if ($this->c->auth->isLoggedIn()) {
            $response = $response->withRedirect($this->c->router->pathFor('adminHome'));
        }
        else {
            $response = $next($request, $response);
        }
        return $response;
    }
}