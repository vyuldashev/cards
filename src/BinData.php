<?php

declare(strict_types=1);

namespace Vyuldashev\Cards;

final class BinData implements Contracts\BinData
{
    private $type;
    private $country;
    private $bank;

    public static function create(int $type, string $country, string $bank): BinData
    {
        return new self($type, $country, $bank);
    }

    private function __construct(int $type, string $country, string $bank)
    {
        $this->type = $type;
        $this->country = $country;
        $this->bank = $bank;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Get bank name.
     *
     * @return string
     */
    public function getBank(): string
    {
        return $this->bank;
    }
}
