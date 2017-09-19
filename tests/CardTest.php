<?php

declare(strict_types=1);

namespace Vyuldashev\Cards\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Vyuldashev\Cards\Card;
use Vyuldashev\Cards\MasterCard;
use Vyuldashev\Cards\Visa;

class CardTest extends TestCase
{
    public function testCreate(): void
    {
        $visa = '4916080075115045';
        $mastercard = '5258369670492716';

        // Visa
        $card = Card::create($visa, 3, 2021, 123);

        $this->assertNotNull($card);
        $this->assertInstanceOf(Visa::class, $card);

        $this->assertSame(Card::TYPE_VISA, $card->getType());
        $this->assertSame($visa, $card->getPan());
        $this->assertSame(3, $card->getExpirationMonth());
        $this->assertSame(2021, $card->getExpirationYear());
        $this->assertSame(123, $card->getCvv());
        $this->assertSame('491608', $card->getBin());
        $this->assertSame('491608******5045', $card->getMaskedPan());

        // MasterCard
        $card = Card::create($mastercard, 9, 2055, 321);

        $this->assertNotNull($card);
        $this->assertInstanceOf(MasterCard::class, $card);

        $this->assertSame(Card::TYPE_MASTERCARD, $card->getType());
        $this->assertSame($mastercard, $card->getPan());
        $this->assertSame(9, $card->getExpirationMonth());
        $this->assertSame(2055, $card->getExpirationYear());
        $this->assertSame(321, $card->getCvv());
        $this->assertSame('525836', $card->getBin());
        $this->assertSame('525836******2716', $card->getMaskedPan());

        // Misc
        $this->assertTrue(Card::create($visa)->passesLuhn());
        $this->assertTrue(Card::create($mastercard)->passesLuhn());
        $this->assertFalse(Card::create('4222222222222222')->passesLuhn());
        $this->assertFalse(Card::create($visa, Carbon::now()->addMonth()->month, Carbon::now()->year)->expired());
        $this->assertTrue(Card::create($visa, Carbon::now()->subMonth()->month, Carbon::now()->year)->expired());
    }
}