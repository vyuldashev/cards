<?php

declare(strict_types=1);

namespace Vyuldashev\Cards\Tests;

use GuzzleHttp\Client;
use Vyuldashev\Cards\Card;
use PHPUnit\Framework\TestCase;
use Vyuldashev\Cards\BinListBinRepository;

class BinListBinRepositoryTest extends TestCase
{
    public function testFind(): void
    {
        $repository = new BinListBinRepository(new Client);

        $result = $repository->find('45717360');

        $this->assertNotNull($result);
        $this->assertSame(Card::TYPE_VISA, $result->getType());
        $this->assertSame('Denmark', $result->getCountry());
        $this->assertSame('Jyske Bank', $result->getBank());
    }
}
