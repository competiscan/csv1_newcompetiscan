<?php

declare(strict_types=1);

// --- Auth Constants ---
if (!defined('AUTH_LOG_DEBUG')) {
    define('AUTH_LOG_DEBUG', 7);
}
if (!defined('AUTH_LOG_INFO')) {
    define('AUTH_LOG_INFO', 6);
}
if (!defined('AUTH_LOG_NOTICE')) {
    define('AUTH_LOG_NOTICE', 5);
}
if (!defined('AUTH_LOG_WARNING')) {
    define('AUTH_LOG_WARNING', 4);
}
if (!defined('AUTH_LOG_ERR')) {
    define('AUTH_LOG_ERR', 3);
}
if (!defined('AUTH_IDLED')) {
    define('AUTH_IDLED', -1);
}
if (!defined('AUTH_LOGOUT')) {
    define('AUTH_LOGOUT', 0);
}
if (!defined('AUTH_EXPIRED')) {
    define('AUTH_EXPIRED', -2);
}
if (!defined('AUTH_WRONG_LOGIN')) {
    define('AUTH_WRONG_LOGIN', -3);
}
if (!defined('AUTH_METHOD_NOT_SUPPORTED')) {
    define('AUTH_METHOD_NOT_SUPPORTED', -4);
}
if (!defined('AUTH_SECURITY_BREACH')) {
    define('AUTH_SECURITY_BREACH', -5);
}
if (!defined('AUTH_CALLBACK_ABORT')) {
    define('AUTH_CALLBACK_ABORT', -6);
}

// --- Auth Class ---
class Auth
{
    private $_storageDriver;
    private $_options;
    private $_loginFunction;
    private $_showLogin;
    private $_storage;
    private $_sessionName;
    private $_idle;
    private $_expire;
    private $_allowLogin;
    private $_advancedSecurity;
    private $_postUsername;
    private $_postPassword;
    private $_postLogout;
    private $_authData;
    private $_status;
    private $_log;
    private $_failedLoginCallback;
    private $_logoutCallback;
    private $_loginCallback;
    private $_checkAuthCallback;
    private ?array $currentUser = null;

    public function __construct($storageDriver, array $options, $loginFunction = null, $showLogin = null)
    {
        $this->_storageDriver = $storageDriver;
        $this->_options = $options;
        $this->_loginFunction = $loginFunction;
        $this->_showLogin = $showLogin;
        $this->_storage = $this->_loadStorage();
        $this->_sessionName = 'PHPSESSID';
        $this->_idle = 0;
        $this->_expire = 0;
        $this->_allowLogin = true;
        $this->_advancedSecurity = false;
        $this->_postUsername = 'username';
        $this->_postPassword = 'password';
        $this->_postLogout = 'logout';
        $this->_authData = [];
        $this->_status = '';
        $this->_log = '';
    }

    private function _loadStorage()
    {
        if ($this->_storageDriver == 'DB') {
            require_once 'Auth/Container/DB.php';
            return new Auth_Container_DB($this->_options);
        }
        return null;
    }

    public function login(string $username, string $password): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        $user = $this->_storage->fetchData($username, $password);
        if (!$user) {
            $this->_status = -3; // AUTH_METHOD_NOT_SUPPORTED or something
            return false;
        }

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'logged_in' => true,
            'username'  => $user[$this->_options['usernamecol']],
            'user_id'   => $user['id'] ?? null,
        ];

        $this->currentUser = $user;
        return true;
    }

    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        // Check for logout request
        if ((isset($_POST[$this->_postLogout]) || isset($_GET[$this->_postLogout])) && $this->isLoggedIn()) {
            $username = $this->getUsername();
            $this->logout();
            if ($this->_logoutCallback && is_callable($this->_logoutCallback)) {
                call_user_func($this->_logoutCallback, $username, $this);
            }
            return;
        }
        
        if ($this->isLoggedIn()) {
            if ($this->_checkAuthCallback && is_callable($this->_checkAuthCallback)) {
                call_user_func($this->_checkAuthCallback, $this->getUsername(), $this);
            }
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$this->_postUsername]) && isset($_POST[$this->_postPassword])) {
            $username = $_POST[$this->_postUsername];
            $password = $_POST[$this->_postPassword];
            if ($this->login($username, $password)) {
                if ($this->_loginCallback && is_callable($this->_loginCallback)) {
                    call_user_func($this->_loginCallback, $username, $this);
                }
            } else {
                if ($this->_failedLoginCallback && is_callable($this->_failedLoginCallback)) {
                    call_user_func($this->_failedLoginCallback, $username, $this);
                }
            }
        } else {
            $showCallback = null;
            if (is_callable($this->_showLogin)) {
                $showCallback = $this->_showLogin;
            } elseif ($this->_showLogin && is_callable($this->_loginFunction)) {
                $showCallback = $this->_loginFunction;
            }

            if ($showCallback) {
                call_user_func($showCallback, $this->_postUsername, $this->_postPassword, $this->_postLogout, $this);
            }
        }
    }

    public function setSessionName($name) { $this->_sessionName = $name; }
    public function setFailedLoginCallback($callback) { $this->_failedLoginCallback = $callback; }
    public function setLogoutCallback($callback) { $this->_logoutCallback = $callback; }
    public function setLoginCallback($callback) { $this->_loginCallback = $callback; }
    public function setCheckAuthCallback($callback) { $this->_checkAuthCallback = $callback; }
    public function setAdvancedSecurity($val) { $this->_advancedSecurity = $val; }
    public function setIdle($time, $add = false) { $this->_idle = $time; }
    public function setExpire($time, $add = false) { $this->_expire = $time; }
    public function setAllowLogin($allow) { $this->_allowLogin = $allow; }
    public function getStatus() { return $this->_status; }

    public function logout(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $this->_status = AUTH_LOGOUT;

        // Clear all session data
        $_SESSION = array();
        
        // Destroy the session
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy();
        }
        
        // Clear any session cookies
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        $this->currentUser = null;
        return true;
    }

    public function setAuth($username) {

        $_SESSION['auth'] = [

            'logged_in' => true,

            'username'  => $username,

        ];

    }

    public function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        return isset($_SESSION['auth']['logged_in']) && $_SESSION['auth']['logged_in'] === true;
    }

    public function getUsername(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        return $_SESSION['auth']['username'] ?? null;
    }

    public function checkAuth(): bool
    {
        return $this->isLoggedIn();
    }

    public function setAuthData($key, $value, $overwrite = true): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        if (!isset($_SESSION['auth_data'])) {
            $_SESSION['auth_data'] = [];
        }
        
        if ($overwrite || !isset($_SESSION['auth_data'][$key])) {
            $_SESSION['auth_data'][$key] = $value;
        }
    }

    public function getAuthData($key)
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        return $_SESSION['auth_data'][$key] ?? null;
    }

}