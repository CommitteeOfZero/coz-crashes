<?php namespace CoZCrashes\Actions;

class AdminAuthenticateAction extends \CoZCrashes\Base {
    public function __invoke ($request, $response, $args) {
        // ignoring args['provider'] as we only support discord
        $provider = new \Wohali\OAuth2\Client\Provider\Discord($this->c->config['oauth']['discord']);

        if (empty($request->getParam('code'))) {
            // Step 1. Get authorization code
            $authUrl = $provider->getAuthorizationUrl(['scope' => ['identify']]);
            $_SESSION['oauth2state'] = $provider->getState();
            return $response->withRedirect($authUrl);
        } elseif (empty($request->getParam('state') || ($request->getParam('state') !== $_SESSION['oauth2state']))) {
            // Screwup or CSRF
            unset($_SESSION['oauth2state']);
            return $response->withStatus(400)->write('Invalid state');
        } else {
            try {
                // Step 2. Get an access token using the provided authorization code
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $request->getParam('code')
                ]);
                // Step 3. (Optional) Look up the user's profile with the provided token
                $user = $provider->getResourceOwner($token);
                $this->c->auth->tryLogIn($user->getId());
                return $response->withRedirect($this->c->router->pathFor('adminHome'));
            } catch(\Exception $e) {
                return $response->withRedirect($this->c->router->pathFor('adminLogin'));
            }
        }
    }
}