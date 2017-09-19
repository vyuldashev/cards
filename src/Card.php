<?php

declare(strict_types=1);

namespace Vyuldashev\Cards;

use Carbon\Carbon;
use Spatie\Regex\Regex;

abstract class Card
{
    public const TYPE_VISA = 1;
    public const TYPE_MASTERCARD = 2;

    protected $type;
    protected $pan;
    protected $expirationMonth;
    protected $expirationYear;
    protected $cvv;

    private static $types = [
        self::TYPE_VISA => [
            'class' => Visa::class,
            'pattern' => '^4[0-9]{12}(?:[0-9]{3})?$'
        ],
        self::TYPE_MASTERCARD => [
            'class' => MasterCard::class,
            'pattern' => '^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$'
        ],
    ];

    public static function create(
        string $pan,
        int $expirationMonth = null,
        int $expirationYear = null,
        int $cvv = null
    ): Card {
        $pan = str_replace(' ', '', $pan);

        foreach (self::$types as $type => $data) {
            $regex = Regex::match('/' . $data['pattern'] . '/', $pan);

            if (!$regex->hasMatch()) {
                continue;
            }

            return new $data['class']($type, $pan, $expirationMonth, $expirationYear, $cvv);
        }

        return null;
    }

    private function __construct(
        int $type,
        string $pan,
        int $expirationMonth = null,
        int $expirationYear = null,
        int $cvv = null
    ) {
        $this->type = $type;
        $this->pan = str_replace(' ', '', $pan);
        $this->expirationMonth = $expirationMonth;
        $this->expirationYear = $expirationYear;
        $this->cvv = $cvv;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getPan(): string
    {
        return $this->pan;
    }

    public function getExpirationMonth(): ?int
    {
        return $this->expirationMonth;
    }

    public function getExpirationYear(): ?int
    {
        return $this->expirationYear;
    }

    public function getCvv(): ?int
    {
        return $this->cvv;
    }

    public function getBin(): string
    {
        return mb_substr($this->pan, 0, 6);
    }

    public function getMaskedPan(int $startDigits = 6, int $endDigits = 4, string $masker = '*'): string
    {
        $masked = mb_strlen($this->pan) - $startDigits - $endDigits;

        return
            mb_substr($this->pan, 0, $startDigits) .
            str_repeat($masker, $masked) .
            mb_substr($this->pan, $endDigits * -1);
    }

    public function passesLuhn(): bool
    {
        return Luhn::check($this->pan);
    }

    public function expired(): bool
    {
        return !Carbon::createFromDate($this->expirationYear, $this->expirationMonth, 1)->isFuture();
    }
}