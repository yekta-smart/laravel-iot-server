<?php

namespace YektaSmart\IotServer\Contracts;

interface IEnvelope extends IMessage
{
    public static function generateId(): int;

    public static function newEnvelope(IMessage $content, ?int $replyTo = null, ?int $id = null): self;

    public function getId(): int;

    public function getReplyTo(): ?int;

    public function getContent(): IMessage;
}
