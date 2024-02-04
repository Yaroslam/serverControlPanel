<?php

namespace Yaroslam\SSH2\Session\Commands\Traits;

trait HasContext
{
    protected array $context = [];

    public function addToContext($context)
    {
        $this->context[] = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
