<?php

namespace test;

use PHPUnit\Framework\TestCase;
use Yaroslam\SSH2\Session\ChainSession;
use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeException;
use Yaroslam\SSH2\Session\Connection\UserPasswordConnection;
use Yaroslam\SSH2\Session\Session;

class ExecutionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->session = new Session(new UserPasswordConnection(), ['port' => 22, 'host' => '194.87.110.114',
            'properties' => ['user' => '', 'password' => '']]);
        $this->chainSession = new ChainSession(new UserPasswordConnection(), ['port' => 22, 'host' => '194.87.110.114',
            'properties' => ['user' => '', 'password' => '']]);
    }

    public function testDefaultSessionSingleExecutionNoError()
    {
        $execRes = $this->session->exec('echo test')->apply();
        $this->assertEquals('test', $execRes['output']);
        $this->assertEquals('', $execRes['error']);
    }

    public function testDefaultSessionSingleExecutionError()
    {
        $execRes = $this->session->exec('echoss test')->apply();
        $this->assertEquals('', $execRes['output']);
        $this->assertStringContainsString('echoss: command not found', $execRes['error']);
    }

    public function testChainSessionSingleExecutionNoError()
    {
        $execRes = $this->chainSession->initChain()->exec('echo test')->apply()->getExecContext();
        $this->assertEquals('0', $execRes['exit_code'][0]);
    }

    public function testChainSessionSingleExecutionError()
    {
        $this->expectException(ExitCodeException::class);
        $this->chainSession->initChain()->exec('echoss test')->apply();
    }
}
