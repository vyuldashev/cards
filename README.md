# Cards

## Installation

You can install the package via composer:

```bash
composer require vyuldashev/cards
```

## Usage

```php
use Vyuldashev\Cards\Card;

Card::create('4916080075115045'); // Vyuldashev\Cards\Visa::class
Card::create('5258369670492716'); // Vyuldashev\Cards\MasterCard::class
```

Pan may also contain non-numeric characters, method `create` will remove these characters itself.

```php
Card::create('4916-0800-7511-5045'); // 4916080075115045
```

Create card with passing expiration month, expiration year and cvv. Each argument is optional:

```php
Card::create('4916080075115045', 3, 2021, 123);
```

If card type cannot be identified, `Unknown` card instance will be returned.

```php
Card::create('8888888888888888'); // Vyuldashev\Cards\Unknown::class
```