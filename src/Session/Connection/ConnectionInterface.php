<?php

namespace Yaroslam\SSH2\Session\Connection;

/**
 * Интерфейс подключения
 */
interface ConnectionInterface
{
    /**
     * Выполняет подключение к переданному $connection, используя $connectProperties
     * @param resource $connection ресурс ssh2 подключения
     * @param array $connectProperties данные подключения
     * @return bool
     */
    public function connect($connection, array $connectProperties): bool;
}
