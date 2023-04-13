<?php

namespace YektaSmart\IotServer;

use YektaSmart\IotServer\Contracts\IEnvelope;
use YektaSmart\IotServer\Contracts\IPeer;

class Peer implements IPeer
{
    /**
     * @var class-string<IEnvelope>|null
     */
    protected ?string $envelope = null;

    public function __construct(protected string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function send(string $data): void
    {
    }

    /**
     * @return class-string<IEnvelope>
     */
    public function getEnvelopeType(): string
    {
        if (null === $this->envelope) {
            throw new \Exception();
        }

        return $this->envelope;
    }

    public function hasEnvelopeType(): bool
    {
        return null !== $this->envelope;
    }

    /**
     * @param class-string<IEnvelope> $type
     */
    public function setEnvelopeType(string $type): void
    {
        $this->envelope = $type;
    }
}
