<?php

namespace YektaSmart\IotServer;

use YektaSmart\IotServer\Contracts\IEventEmitter;

class EventEmitter implements IEventEmitter
{
    /**
     * @var array<string,array{once:bool,cb:callable,timeout:int|null}>
     */
    protected $listeners = [];

    /**
     * @param callable(IClient $client, callable($message) $reply):mixed $cb
     */
    public function on(string $event, callable $cb): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = [
            'once' => false,
            'cb' => $cb,
            'timeout' => null,
        ];
    }

    /**
     * @param callable(IClient $client, callable($message) $reply):mixed $cb
     */
    public function once(string $event, callable $cb, ?int $timeout = null): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = [
            'once' => true,
            'cb' => $cb,
            'timeout' => $timeout,
        ];
    }

    /**
     * @param callable $cb
     */
    public function off(string $event, ?callable $cb = null): void
    {
        if (null === $cb) {
            unset($this->listeners[$event]);

            return;
        }
        if (!isset($this->listeners[$event])) {
            return;
        }
        for ($x = 0, $l = count($this->listeners[$event]); $x < $l; ++$x) {
            if ($this->listeners[$event][$x]['cb'] === $cb) {
                array_splice($this->listeners[$event], $x, 1);
                --$l;
            }
        }
        if (0 === $l) {
            unset($this->listeners[$event]);
        }
    }

    /**
     * @param mixed[] $args
     */
    public function emit(string $event, mixed ...$args): bool
    {
        if (!isset($this->listeners[$event])) {
            return false;
        }
        $l = count($this->listeners[$event]);
        if (0 === $l) {
            return false;
        }
        for ($x = 0; $x < $l; ++$x) {
            call_user_func_array($this->listeners[$event][$x]['cb'], $args);
            if ($this->listeners[$event][$x]['once']) {
                array_splice($this->listeners[$event], $x, 1);
                --$l;
            }
        }

        return true;
    }

    public function clearExpired(): void
    {
        $now = time();
        foreach (array_keys($this->listeners) as $event) {
            for ($x = 0, $l = count($this->listeners[$event]); $x < $l; ++$x) {
                if (
                    $this->listeners[$event][$x]['timeout'] and
                    $now > $this->listeners[$event][$x]['timeout']
                ) {
                    array_splice($this->listeners[$event], $x, 1);
                    --$l;
                }
            }
        }
    }
}
