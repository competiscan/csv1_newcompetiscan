<?php
if (!defined('AUTH_SKIP_COOKIE_AUTH')) {
    define('AUTH_SKIP_COOKIE_AUTH', true);
}
require_once "auth_inc.php";

doLogout('', null);
?>