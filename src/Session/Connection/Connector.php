<?php

namespace Yaroslam\SSH2\Session\Connection;

/**
 * Класс обертка для подключений, инкапсулирующем в себе логику подключения, по переданному типу подключения
 */
class Connector
{
    /**
     * @var ConnectionInterface Подключение
     */
    private ConnectionInterface $connection;

    /**
     * @var string|mixed наименование хоста, к которому будет подключение
     */
    private string $host;

    /**
     * @var int|mixed порт хоста, к которому будет подключение
     */
    private int $port;

    /**
     * @var resource ресурс ssh2 подключения
     */
    private $ssh2Connect;

    /**
     * @var array Настройки подключения
     */
    private array $connectProperties;

    /**
     * Конструктор класса
     *
     * @param  ConnectionInterface  $connection тип подключения
     * @param  array  $connectProperties настройки подключения
     */
    public function __construct(ConnectionInterface $connection, array $connectProperties)
    {
        $this->port = $connectProperties['port'];
        $this->host = $connectProperties['host'];
        $this->connectProperties = $connectProperties;
        $this->connection = $connection;
    }

    /**
     * Выполняет подключение, согласно всем настройкам класса
     */
    public function connect(): void
    {
        $this->ssh2Connect = ssh2_connect($this->host, $this->port);
        $this->connection->connect($this->ssh2Connect, $this->connectProperties['properties']);
    }

    /**
     * Возвращает ресурс ssh2 подключения
     *
     * @return resource
     */
    public function getSsh2Connect()
    {
        return $this->ssh2Connect;
    }

    /**
     * Выполняет дисконект
     */
    public function disconnect(): bool
    {
        return ssh2_disconnect($this->ssh2Connect);
    }
}
