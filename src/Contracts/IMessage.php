<?php

namespace YektaSmart\IotServer\Contracts;

interface IMessage
{
    /**
     * @return string
     */
    public function serializeToString();

    /**
     * @param string $data
     *
     * @return void
     */
    public function mergeFromString($data);
}
