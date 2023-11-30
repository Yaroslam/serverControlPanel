<?php

namespace Yaroslam\SSH2\Session;

class Session extends AbstractSession
{
    private array $context;

    public function exec(string $cmdCommand)
    {
        $stream = ssh2_exec($this->connector->getConnectionTunnel(), $cmdCommand);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);
        $this->context = ['output' => stream_get_contents($stream), 'error' => stream_get_contents($errorStream)];
        fclose($errorStream);
        fclose($stream);

        return $this;
    }

    public function apply()
    {
        return $this->context;
    }
}
