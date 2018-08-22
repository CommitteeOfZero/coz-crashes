<?php namespace CoZCrashes;

class Auth extends Base {
    protected $loggedIn = null;

    public function isLoggedIn() {
        if (isset($this->loggedIn)) return $this->loggedIn;
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $this->loggedIn = isset($_SESSION['user_id']) && $this->c->db->table('users')->find($_SESSION['user_id']) !== null;
        return $this->loggedIn;
    }

    public function tryLogIn($id) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        unset($this->loggedIn);
        $_SESSION['user_id'] = $id;
        return $this->isLoggedIn();
    }
}