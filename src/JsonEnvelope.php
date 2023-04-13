<?php

namespace YektaSmart\IotServer;

use YektaSmart\IotServer\Contracts\IEnvelope;
use YektaSmart\IotServer\Contracts\IMessage;

/**
 * @property int      $id
 * @property IMessage $content
 * @property int|null $replyTo
 */
class JsonEnvelope extends JsonMessage implements IEnvelope
{
    public static function generateId(): int
    {
        return rand(PHP_INT_MIN, PHP_INT_MAX);
    }

    public static function newEnvelope(IMessage $content, ?int $replyTo = null, ?int $id = null): self
    {
        $e = new self();
        $e->id = $id ?? self::generateId();
        $e->replyTo = $replyTo;
        $e->content = $content;

        return $e;
    }

    public function __construct()
    {
        parent::__construct('Envelope');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReplyTo(): ?int
    {
        return $this->replyTo;
    }

    public function getContent(): JsonMessage
    {
        return $this->content;
    }

    public function mergeFromString($data): void
    {
        parent::mergeFromString($data);
        if (!isset($this->payload['content']['@type'])) {
            throw new \Exception('@type field is missing');
        }
        $type = $this->payload['content']['@type'];
        unset($this->payload['content']['@type']);
        $this->payload['content'] = new JsonMessage($type, $this->payload['content']);
    }
}
