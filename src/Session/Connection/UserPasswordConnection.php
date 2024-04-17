<?php

namespace Yaroslam\SSH2\Session\Connection;

/**
 * Класс подключения по имени и паролю пользователя
 */
class UserPasswordConnection implements ConnectionInterface
{
    public $c = 0;

    public function connect($connection, array $connectProperties): bool
    {
        return ssh2_auth_password($connection, $connectProperties['user'], $connectProperties['password']);
    }
}
