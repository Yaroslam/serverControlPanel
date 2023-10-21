<?php
namespace Happy\ServerControlPanel\Session\Connection;
interface ConnectionInterface
{
    public function connect($connection, array $connectProperties);

}