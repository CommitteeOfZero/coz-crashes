<?php namespace CoZCrashes\Middleware;

class AuthMiddleware extends \CoZCrashes\Base {
    public function __invoke($request, $response, $next) {
        if (!$this->c->auth->isLoggedIn()) {
            $response = $response->withRedirect($this->c->router->pathFor('adminLogin'));
        }
        else {
            $response = $next($request, $response);
        }
        return $response;
    }
}