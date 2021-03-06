<?php

declare(strict_types=1);

namespace Vyuldashev\Cards;

use Serializable;
use Carbon\Carbon;
use JsonSerializable;
use RuntimeException;
use Spatie\Regex\Regex;
use Vyuldashev\Cards\Exceptions\InvalidPanException;

abstract class Card implements Contracts\Card, JsonSerializable, Serializable
{
    public const TYPE_UNKNOWN = 0;
    public const TYPE_VISA = 1;
    public const TYPE_MASTERCARD = 2;
    public const TYPE_AMERICAN_EXPRESS = 3;
    public const TYPE_DINERS_CLUB = 4;
    public const TYPE_DISCOVER = 5;
    public const TYPE_JCB = 6;

    protected $type;
    protected $pan;
    protected $expirationMonth;
    protected $expirationYear;
    protected $cvv;

    private static $types = [
        self::TYPE_VISA => [
            'class' => Visa::class,
            'pattern' => '^4[0-9]{12}(?:[0-9]{3})?$',
        ],
        self::TYPE_MASTERCARD => [
            'class' => MasterCard::class,
            'pattern' => '^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$',
        ],
        self::TYPE_AMERICAN_EXPRESS => [
            'class' => AmericanExpress::class,
            'pattern' => '^3[47][0-9]{13}$',
        ],
        self::TYPE_DINERS_CLUB => [
            'class' => DinersClub::class,
            'pattern' => '^3(?:0[0-5]|[68][0-9])[0-9]{11}$',
        ],
        self::TYPE_DISCOVER => [
            'class' => Discover::class,
            'pattern' => '^6(?:011|5[0-9]{2})[0-9]{12}$',
        ],
        self::TYPE_JCB => [
            'class' => JCB::class,
            'pattern' => '^(3(?:088|096|112|158|337|5(?:2[89]|[3-8][0-9]))\d{12})$',
        ],
    ];

    /**
     * Create a new Card instance.
     *
     * @param string $pan
     * @param int|null $expirationMonth
     * @param int|null $expirationYear
     * @param int|null $cvv
     *
     * @return Card
     * @throws InvalidPanException
     */
    public static function create(
        string $pan,
        int $expirationMonth = null,
        int $expirationYear = null,
        int $cvv = null
    ): Card {
        if (self::cleanPan($pan) === '') {
            throw new InvalidPanException('Pan "'.$pan.'" is invalid.');
        }

        $pan = self::cleanPan($pan);

        foreach (self::$types as $type => $data) {
            $regex = Regex::match('/'.$data['pattern'].'/', $pan);

            if (!$regex->hasMatch()) {
                continue;
            }

            return new $data['class']($type, $pan, $expirationMonth, $expirationYear, $cvv);
        }

        return new Unknown(self::TYPE_UNKNOWN, $pan, $expirationMonth, $expirationYear, $cvv);
    }

    public static function validate(string $pan): bool
    {
        return Luhn::check($pan);
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

    /**
     * Get card type.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Get PAN.
     *
     * @return string
     */
    public function getPan(): string
    {
        return $this->pan;
    }

    /**
     * Get card expiration month.
     *
     * @return int|null
     */
    public function getExpirationMonth(): ?int
    {
        return $this->expirationMonth;
    }

    /**
     * Get card expiration year.
     *
     * @return int|null
     */
    public function getExpirationYear(): ?int
    {
        return $this->expirationYear;
    }

    /**
     * Get CVV.
     *
     * @return int|null
     */
    public function getCvv(): ?int
    {
        if (is_string($this->cvv)) {
            return null;
        }

        return $this->cvv;
    }

    /**
     * Get BIN number.
     *
     * @return string
     */
    public function getBin(): string
    {
        return mb_substr($this->pan, 0, 6);
    }

    /**
     * Get masked pan.
     *
     * @param int $startDigits
     * @param int $endDigits
     * @param string $masker
     *
     * @return string
     */
    public function getMaskedPan(int $startDigits = 6, int $endDigits = 4, string $masker = '*'): string
    {
        $masked = mb_strlen($this->pan) - $startDigits - $endDigits;

        return
            mb_substr($this->pan, 0, $startDigits).
            str_repeat($masker, $masked).
            mb_substr($this->pan, $endDigits * -1);
    }

    /**
     * Get masked cvv.
     *
     * @param string $masker
     *
     * @return string|null
     */
    public function getMaskedCvv(string $masker = '*'): ?string
    {
        if ($this->cvv === null) {
            return null;
        }

        return str_repeat($masker, mb_strlen((string) $this->cvv));
    }

    /**
     * Determine if pan is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return Luhn::check($this->pan);
    }

    /**
     * Determine if card has been expired.
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function expired(): bool
    {
        if ($this->expirationYear === null || $this->expirationMonth === null) {
            throw new RuntimeException('Card expiration data should be filled.');
        }

        return !Carbon::createFromDate($this->expirationYear, $this->expirationMonth, 1)->isFuture();
    }

    public function __toString(): string
    {
        return $this->getMaskedPan();
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'pan' => $this->getMaskedPan(),
            'expiration_month' => $this->expirationMonth,
            'expiration_year' => $this->expirationYear,
            'cvv' => $this->getMaskedCvv(),
        ];
    }

    /**
     * String representation of object.
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize(): string
    {
        return json_encode($this->jsonSerialize());
    }

    public function unserialize($serialized): void
    {
        $json = json_decode($serialized, true);

        $this->type = $json['type'];
        $this->pan = $json['pan'];
        $this->expirationMonth = $json['expiration_month'];
        $this->expirationYear = $json['expiration_year'];
        $this->cvv = $json['cvv'];
    }

    private static function cleanPan(string $pan): string
    {
        return Regex::replace('/[^0-9]/', '', $pan)->result();
    }
}
