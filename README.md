# VCard Generator for PHP

A lightweight, framework-agnostic VCard 3.0/4.0 generator for PHP 8.2+ with zero dependencies. 

## Requirements

- PHP 8.2 or higher

## Installation

You can install the package via composer:

```bash
composer require abduns/vcard
```

## Usage

Building a VCard is simple and intuitive using the fluent builder pattern.

```php
use Dunn\VCard\VCard;

$vcard = VCard::make('3.0') // Default is 3.0
    ->addName(lastName: 'Doe', firstName: 'John', prefix: 'Mr.')
    ->addCompany('Acme Corp')
    ->addJobTitle('Software Engineer')
    ->addEmail('john.doe@example.com', 'WORK')
    ->addPhoneNumber('+1234567890', 'CELL')
    ->addAddress(
        name: '', 
        extended: 'Suite 100', 
        street: '123 Main St', 
        city: 'New York', 
        region: 'NY', 
        zip: '10001', 
        country: 'USA'
    )
    ->addUrl('https://example.com')
    ->addNote('Hello world!');

// Get the raw VCard string
$content = $vcard->build();

// You can now save $content to a .vcf file or output it to the browser.
```

### Available Methods

- `addName(string $lastName, string $firstName, string $additional = '', string $prefix = '', string $suffix = '')`
- `addFormattedName(string $formattedName)`
- `addCompany(string $company)`
- `addJobTitle(string $jobTitle)`
- `addEmail(string $email, string $type = 'INTERNET')`
- `addPhoneNumber(string $number, string $type = 'CELL')`
- `addAddress(string $name, string $extended, string $street, string $city, string $region, string $zip, string $country, string $type = 'WORK')`
- `addUrl(string $url, string $type = 'WORK')`
- `addNote(string $note)`
- `addPhoto(string $urlOrBase64, string $type = 'JPEG')`

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
