<?php
namespace Yaroslam\SSH2\Session\Connection;
interface ConnectionInterface
{
    public function connect($connection, array $connectProperties);

}