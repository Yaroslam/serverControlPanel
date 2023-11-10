<?php
namespace Yaroslam\ServerControlPanel\Session\Connection;
interface ConnectionInterface
{
    public function connect($connection, array $connectProperties);

}