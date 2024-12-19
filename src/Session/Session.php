<?php

namespace Yaroslam\SSH2\Session;

/**
 * Класс сессии, которая может выполнять только exec команды, не сохраняет своего состояния между выполнениями
 *
 * @todo добавить историчность выполнения команд
 */
class Session extends AbstractSession
{
    /**
     * @var array<string, string> контекст выполнения
     */
    private array $context;

    public function exec(string $cmdCommand): AbstractSession
    {
        $stream = ssh2_exec($this->connector->getSsh2Connect(), $cmdCommand);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);
        $this->context = ['output' => trim(stream_get_contents($stream)), 'error' => trim(stream_get_contents($errorStream))];
        fclose($errorStream);
        fclose($stream);

        return $this;
    }

    /**
     * Возвращает контекст выполнения подключения
     * @return array<string, string>
     */
    public function apply(): array
    {
        return $this->context;
    }
}
