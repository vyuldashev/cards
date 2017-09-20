<?php

declare(strict_types=1);

namespace Vyuldashev\Cards\Contracts;

interface Card
{
    /**
     * Get card type.
     *
     * @return int
     */
    public function getType(): int;

    /**
     * Get PAN.
     *
     * @return string
     */
    public function getPan(): string;

    /**
     * Get card expiration month.
     *
     * @return int|null
     */
    public function getExpirationMonth(): ?int;

    /**
     * Get card expiration year.
     *
     * @return int|null
     */
    public function getExpirationYear(): ?int;

    /**
     * Get CVV.
     *
     * @return int|null
     */
    public function getCvv(): ?int;

    /**
     * Get BIN number.
     *
     * @return string
     */
    public function getBin(): string;

    /**
     * Get masked pan.
     *
     * @param int $startDigits
     * @param int $endDigits
     * @param string $masker
     *
     * @return string
     */
    public function getMaskedPan(int $startDigits = 6, int $endDigits = 4, string $masker = '*'): string;

    /**
     * Determine if pan is valid.
     *
     * @return bool
     */
    public function valid(): bool;

    /**
     * Determine if card has been expired.
     *
     * @return bool
     */
    public function expired(): bool;
}
