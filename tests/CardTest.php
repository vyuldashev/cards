<?php

declare(strict_types=1);

namespace Vyuldashev\Cards\Tests;

use Carbon\Carbon;
use Vyuldashev\Cards\JCB;
use Vyuldashev\Cards\Card;
use Vyuldashev\Cards\Visa;
use Vyuldashev\Cards\Unknown;
use Vyuldashev\Cards\Discover;
use PHPUnit\Framework\TestCase;
use Vyuldashev\Cards\DinersClub;
use Vyuldashev\Cards\MasterCard;
use Vyuldashev\Cards\AmericanExpress;
use Vyuldashev\Cards\Exceptions\InvalidPanException;

class CardTest extends TestCase
{
    public function testCreateNoDigits(): void
    {
        $this->expectException(InvalidPanException::class);
        $this->expectExceptionMessage('Pan "----" is invalid.');

        $this->assertNull(Card::create('----'));
    }

    public function testCreateWithNotNumericalCharacters(): void
    {
        $this->assertInstanceOf(Card::class, Card::create('4916-0800-7511-5045'));
    }

    public function testCreateUnknownType(): void
    {
        $this->assertInstanceOf(Unknown::class, Card::create(str_repeat('8', 16)));
    }

    public function testCreateVisa()
    {
        $pan = '4916080075115045';

        $card = Card::create($pan, 3, 2021, 123);

        $this->assertNotNull($card);
        $this->assertInstanceOf(Visa::class, $card);

        $this->assertSame(Card::TYPE_VISA, $card->getType());
        $this->assertSame($pan, $card->getPan());
        $this->assertSame(3, $card->getExpirationMonth());
        $this->assertSame(2021, $card->getExpirationYear());
        $this->assertSame(123, $card->getCvv());
        $this->assertSame('491608', $card->getBin());
        $this->assertSame('491608******5045', $card->getMaskedPan());
        $this->assertSame('491608******5045', (string) $card);
        $this->assertSame('***', $card->getMaskedCvv());
        $this->assertTrue($card->valid());
        $this->assertFalse(Card::create($pan, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($pan, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }

    public function testCreateMastercard(): void
    {
        $pan = '5258369670492716';

        $card = Card::create($pan, 9, 2055, 321);

        $this->assertNotNull($card);
        $this->assertInstanceOf(MasterCard::class, $card);

        $this->assertSame(Card::TYPE_MASTERCARD, $card->getType());
        $this->assertSame($pan, $card->getPan());
        $this->assertSame(9, $card->getExpirationMonth());
        $this->assertSame(2055, $card->getExpirationYear());
        $this->assertSame(321, $card->getCvv());
        $this->assertSame('525836', $card->getBin());
        $this->assertSame('525836******2716', $card->getMaskedPan());
        $this->assertSame('525836******2716', (string) $card);
        $this->assertTrue($card->valid());
        $this->assertFalse(Card::create($pan, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($pan, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }

    public function testCreateAmericanExpress(): void
    {
        $pan = '344601216248575';

        $card = Card::create($pan, 9, 2055, 321);

        $this->assertNotNull($card);
        $this->assertInstanceOf(AmericanExpress::class, $card);

        $this->assertSame(Card::TYPE_AMERICAN_EXPRESS, $card->getType());
        $this->assertSame($pan, $card->getPan());
        $this->assertSame(9, $card->getExpirationMonth());
        $this->assertSame(2055, $card->getExpirationYear());
        $this->assertSame(321, $card->getCvv());
        $this->assertSame('344601', $card->getBin());
        $this->assertSame('344601*****8575', $card->getMaskedPan());
        $this->assertSame('344601*****8575', (string) $card);
        $this->assertTrue($card->valid());
        $this->assertFalse(Card::create($pan, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($pan, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }

    public function testCreateDinersClub(): void
    {
        $pan = '36653199918081';

        $card = Card::create($pan, 9, 2055, 321);

        $this->assertNotNull($card);
        $this->assertInstanceOf(DinersClub::class, $card);

        $this->assertSame(Card::TYPE_DINERS_CLUB, $card->getType());
        $this->assertSame($pan, $card->getPan());
        $this->assertSame(9, $card->getExpirationMonth());
        $this->assertSame(2055, $card->getExpirationYear());
        $this->assertSame(321, $card->getCvv());
        $this->assertSame('366531', $card->getBin());
        $this->assertSame('366531****8081', $card->getMaskedPan());
        $this->assertSame('366531****8081', (string) $card);
        $this->assertTrue($card->valid());
        $this->assertFalse(Card::create($pan, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($pan, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }

    public function testCreateDiscover(): void
    {
        $pan = '6011871402720963';

        $card = Card::create($pan, 9, 2055, 321);

        $this->assertNotNull($card);
        $this->assertInstanceOf(Discover::class, $card);

        $this->assertSame(Card::TYPE_DISCOVER, $card->getType());
        $this->assertSame($pan, $card->getPan());
        $this->assertSame(9, $card->getExpirationMonth());
        $this->assertSame(2055, $card->getExpirationYear());
        $this->assertSame(321, $card->getCvv());
        $this->assertSame('601187', $card->getBin());
        $this->assertSame('601187******0963', $card->getMaskedPan());
        $this->assertSame('601187******0963', (string) $card);
        $this->assertTrue($card->valid());
        $this->assertFalse(Card::create($pan, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($pan, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }

    public function testCreateJCB(): void
    {
        $pan = '3158731450703123';

        $card = Card::create($pan, 9, 2055, 321);

        $this->assertNotNull($card);
        $this->assertInstanceOf(JCB::class, $card);

        $this->assertSame(Card::TYPE_JCB, $card->getType());
        $this->assertSame($pan, $card->getPan());
        $this->assertSame(9, $card->getExpirationMonth());
        $this->assertSame(2055, $card->getExpirationYear());
        $this->assertSame(321, $card->getCvv());
        $this->assertSame('315873', $card->getBin());
        $this->assertSame('315873******3123', $card->getMaskedPan());
        $this->assertSame('315873******3123', (string) $card);
        $this->assertTrue($card->valid());
        $this->assertFalse(Card::create($pan, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($pan, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }

    public function testValidate(): void
    {
        $this->assertTrue(Card::validate('4916080075115045'));
        $this->assertTrue(Card::validate('5258369670492716'));
        $this->assertFalse(Card::validate('4222222222222222'));
    }

    public function testJsonSerializable(): void
    {
        $card = Card::create('4916080075115045', 5, 2053, 123);

        $expected = [
            'type' => 1,
            'pan' => '491608******5045',
            'expiration_month' => 5,
            'expiration_year' => 2053,
            'cvv' => '***',
        ];

        $this->assertSame($expected, json_decode(json_encode($card), true));
    }

    public function testSerializable(): void
    {
        $card = Card::create('4916080075115045', 5, 2053, 123);

        $expected = [
            'type' => 1,
            'pan' => '491608******5045',
            'expiration_month' => 5,
            'expiration_year' => 2053,
        ];

        $serialized = serialize($card);

        /** @var Card $unserialized */
        $unserialized = unserialize($serialized, ['allowed_classes' => [Visa::class]]);

        $this->assertInstanceOf(Visa::class, $unserialized);
        $this->assertSame(Card::TYPE_VISA, $unserialized->getType());
        $this->assertSame($expected['pan'], $unserialized->getPan());
        $this->assertSame($expected['expiration_month'], $unserialized->getExpirationMonth());
        $this->assertSame($expected['expiration_year'], $unserialized->getExpirationYear());
        $this->assertNull($unserialized->getCvv());
    }
}
