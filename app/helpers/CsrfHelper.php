<?php
// app/helpers/CsrfHelper.php

class CsrfHelper
{
    public static function ensureToken()
    {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function token()
    {
        return self::ensureToken();
    }

    public static function validateRequest()
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (!is_string($sessionToken) || $sessionToken === '') {
            return false;
        }

        $requestToken = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!is_string($requestToken) || $requestToken === '') {
            return false;
        }

        return hash_equals($sessionToken, $requestToken);
    }
}
