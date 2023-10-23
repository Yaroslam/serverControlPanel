<?php

namespace Happy\ServerControlPanel\Session\Connection;

class UserPasswordConnection implements ConnectionInterface
{
    public function connect($connection, array $connectProperties)
    {
        ssh2_auth_password($connection, $connectProperties['user'], $connectProperties['password']);
    }
}
