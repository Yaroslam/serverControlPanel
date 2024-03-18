<?php

namespace test;

require_once __DIR__.'/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use Yaroslam\SSH2\Session\Connection\UserPasswordConnection;

class ConnectionsTest extends TestCase
{
    public function testUserPasswordConn()
    {
        $connector = new UserPasswordConnection();
        $this->assertTrue($connector->connect(ssh2_connect('', 22),
            ['user' => '', 'password' => '']), 'error during connect by password and login');
    }
}
