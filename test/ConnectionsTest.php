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
        $this->assertTrue($connector->connect(ssh2_connect($_ENV['host'], 22),
            ['user' => $_ENV['user'], 'password' => $_ENV['password']]), 'error during connect by password and login');
    }
}
