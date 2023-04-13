<?php

namespace YektaSmart\IotServer;

use YektaSmart\IotServer\Contracts\IMessage;

class JsonMessage implements IMessage, \Stringable, \JsonSerializable
{
    /**
     * @param array<mixed,mixed> $payload
     */
    public function __construct(protected string $_type, protected array $payload = [])
    {
    }

    public function __set($name, $value)
    {
        $this->payload[$name] = $value;
    }

    public function __get($name)
    {
        return $this->payload[$name] ?? null;
    }

    public function __isset($name): bool
    {
        return isset($this->payload[$name]);
    }

    public function __unset($name)
    {
        unset($this->payload[$name]);
    }

    public function __toString(): string
    {
        return $this->serializeToString();
    }

    public function getType(): string
    {
        return $this->_type;
    }

    public function serializeToString(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param string $data
     */
    public function mergeFromString($data): void
    {
        $data = json_decode($data, true);
        if (!isset($data['@type'])) {
            throw new \Exception('@type field is missing');
        }
        $this->_type = $data['@type'];
        unset($data['@type']);
        $this->payload = $data;
    }

    public function jsonSerialize(): mixed
    {
        return [
            '@type' => $this->_type,
            ...$this->payload,
        ];
    }
}
