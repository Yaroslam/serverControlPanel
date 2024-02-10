<?php

namespace Yaroslam\SSH2\Session;

use Yaroslam\SSH2\Session\Connection\ConnectionInterface;
use Yaroslam\SSH2\Session\Connection\Connector;

/**
 * Класс абстрактной сессии подключения
 */
abstract class AbstractSession
{
    /**
     * @var Connector коннектор, инкапсулирующий логику подключения к серверу
     */
    protected Connector $connector;

    /**
     * Конструктор класса
     * @param ConnectionInterface $connectionType тип подключения
     * @param array $connectProperties настройки подключения
     */
    public function __construct(ConnectionInterface $connectionType, array $connectProperties)
    {
        $this->connector = new Connector($connectionType, $connectProperties);
        $this->connector->connect();
    }


    /**
     * @return mixed
     */
    abstract public function apply(): mixed;

    /**
     * Выполняет exec команду с переданным текстом команды
     * @param string $cmdCommand текст команды
     * @return AbstractSession
     */
    abstract public function exec(string $cmdCommand): AbstractSession;

    /**
     *
     */
    public function __destruct()
    {
        $this->connector->disconnect();
    }
}
