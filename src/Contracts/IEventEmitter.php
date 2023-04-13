<?php

namespace YektaSmart\IotServer\Contracts;

interface IEventEmitter
{
    public function on(string $event, callable $cb): void;

    public function once(string $event, callable $cb, ?int $timeout = null): void;

    public function off(string $event, ?callable $cb = null): void;

    public function emit(string $event, mixed ...$args): bool;

    public function clearExpired(): void;
}
