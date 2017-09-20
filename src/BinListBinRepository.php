<?php

declare(strict_types=1);

namespace Vyuldashev\Cards;

use GuzzleHttp\Client;
use Vyuldashev\Cards\Contracts\BinRepository;

class BinListBinRepository implements BinRepository
{
    private $url = 'https://lookup.binlist.net';

    private $guzzle;

    private $scheme = [
        'visa' => Card::TYPE_VISA,
        'mastercard' => Card::TYPE_MASTERCARD,
    ];

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function find(string $bin): ?Contracts\BinData
    {
        $response = $this->guzzle->get($this->url.'/'.$bin, [
            'headers' => [
                'Accept-Version' => 3,
            ],
        ]);

        $json = json_decode((string) $response->getBody(), true);

        $type = $this->scheme[$json['scheme']] ?: Card::TYPE_UNKNOWN;

        return BinData::create($type, $json['country']['name'], $json['bank']['name']);
    }
}
