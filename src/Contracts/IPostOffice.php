<?php

namespace YektaSmart\IotServer\Contracts;

interface IPostOffice extends IEventEmitter
{
    public function send(IPeer $peer, IMessage $content, ?callable $cb = null, int|IEnvelope|null $replyTo = null): void;

    public function receive(IPeer $peer, string|IEnvelope $envelope): void;
}
