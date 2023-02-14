<?php

namespace dnj\AAA\Contracts;

interface IOwnerableModel
{
    public function getOwnerUserId(): ?int;

    public function getOwnerUserColumn(): string;
}
