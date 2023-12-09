<?php

namespace Yaroslam\SSH2\Session\Connection;

class OpenKeyConnection implements ConnectionInterface
{
    public function connect($connection, array $connectProperties): bool
    {
        return ssh2_auth_pubkey_file($connection,
            $connectProperties['user'],
            $connectProperties['pubkeyfile'],
            $connectProperties['privkeyfile'],
            array_key_exists('password', $connectProperties) ?
                $connectProperties['password'] : null);
    }
}
