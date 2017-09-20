<?php

declare(strict_types=1);

namespace Vyuldashev\Cards\Contracts;

interface BinRepository
{
    public function find(string $bin): ?BinData;
}
