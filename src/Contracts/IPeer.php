<?php

namespace YektaSmart\IotServer\Contracts;

interface IPeer
{
    public function getId(): string;

    public function getEnvelopeType(): string;

    public function hasEnvelopeType(): bool;

    public function setEnvelopeType(string $type): void;

    public function send(string $data): void;
}
