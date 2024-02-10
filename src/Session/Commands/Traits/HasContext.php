<?php

namespace Yaroslam\SSH2\Session\Commands\Traits;

/**
 * Трейт для команд, имеющих внутренний контекст выполнения
 */
trait HasContext
{
    /**
     * @var array внутренний контекст выполнения
     */
    protected array $context = [];

    /**
     * Добавление контекста во внутренний
     * @api
     * @param mixed $context контекст выполнения команды
     * @return void
     */
    public function addToContext(mixed $context): void
    {
        $this->context[] = $context;
    }

    /**
     * Возвращает контекст выполнения
     * @api
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
