# vcard

A framework-agnostic VCard 3.0/4.0 generator for PHP 8.2+.

[![Tests](https://github.com/abduns/vcard/actions/workflows/tests.yml/badge.svg)](https://github.com/abduns/vcard/actions)
[![Coverage](https://img.shields.io/endpoint?url=https://raw.githubusercontent.com/abduns/vcard/main/coverage.json)](https://github.com/abduns/vcard)
[![Version](https://img.shields.io/packagist/v/abduns/vcard.svg)](https://packagist.org/packages/abduns/vcard)
[![Downloads](https://img.shields.io/packagist/dt/abduns/vcard.svg)](https://packagist.org/packages/abduns/vcard)
[![License](https://img.shields.io/packagist/l/abduns/vcard.svg)](LICENSE.md)

---

## Features

- Modern PHP support
- Lightweight and fast
- Typed API
- Framework agnostic
- Standards-oriented (RFC 6350)
- All 50 IANA registered properties supported
- Zero external dependencies

---

## Installation

```bash
composer require abduns/vcard
```

---

## Quick Start

```php
use Dunn\VCard\VCard;

$vcf = VCard::make()
    ->addName('Doe', 'John')
    ->addEmail('john@example.com', 'work')
    ->addPhoneNumber('+62-812-3456-7890', 'cell')
    ->addUrl('https://johndoe.com')
    ->build();

echo $vcf;
```

Example output:

```vcf
BEGIN:VCARD
VERSION:4.0
N:Doe;John;;;
FN:John Doe
EMAIL;TYPE=work:john@example.com
TEL;TYPE=cell:+62-812-3456-7890
URL;TYPE=work:https://johndoe.com
REV:20240101T000000Z
END:VCARD
```

---

## Why This Package?

- Existing solutions are outdated
- Missing modern PHP features
- Poor developer experience
- No standards compliance
- Too framework-coupled

This package focuses on simplicity, interoperability, and modern developer ergonomics. Most existing PHP vCard libraries use outdated binary encoding instead of `data:` URIs and aren't aligned with RFC 6350.

---

## Usage

### Basic Usage

```php
use Dunn\VCard\VCard;

VCard::make()->addName(
    lastName:   'Doe',
    firstName:  'John',
    additional: '',
    prefix:     'Dr.',
    suffix:     'Jr.'
)
->addEmail('john@example.com', 'work')
->addPhoneNumber('+62-812-3456-7890', 'cell');
```

### Advanced Usage

```php
// Embedded (raw base64 → automatically wrapped as data: URI)
->addPhoto($base64String, 'image/jpeg')

// Social Profiles
->addSocialProfile('https://github.com/johndoe', 'GitHub')

// UID (recommended for sync)
->addUid('urn:uuid:' . $uuid)

// Custom Properties
->addProperty('X-CUSTOM-FIELD', 'value')
```

### Configuration

```php
// The only enforced requirement at runtime is FN (Formatted Name).
// OK — addName() sets FN automatically
VCard::make()->addName('Doe', 'John')->build();
```

---

## Standards / Specifications

Designed around official Internet standards and IANA registries.

- RFC 6350 (vCard 4.0)
- RFC 6474 (Birth/Death)
- RFC 6715 (Expertise/Hobby)
- RFC 8605 (CONTACT-URI)
- RFC 9554
- RFC 9555

References:

- https://www.iana.org/assignments/vcard-elements/vcard-elements.xhtml

---

## Supported Features

| Feature | Support |
|---|---|
| RFC 6350 Validation | ✅ |
| All IANA Properties | ✅ |
| Custom Properties | ✅ |

---

## Compatibility

| Platform | Supported |
|---|---|
| PHP 8.2+ | ✅ |
| Apple Contacts | ✅ |
| Google Contacts | ✅ |

---

## Design Goals

- Developer experience first
- Predictable APIs
- Minimal dependencies
- Strong typing
- Extensibility
- Interoperability

---

## Architecture

- immutable objects
- fluent builder pattern
- strong typing on inputs

---

## Performance

| Operation | Time |
|---|---|
| Build typical vCard | < 1ms |

---

## Testing

```bash
composer test
```

---

## Roadmap

- [ ] Validation API
- [ ] vCard parsing / reading
- [ ] jCard (JSON) support
- [ ] xCard (XML) support

---

## Contributing

Contributions, issues, and discussions are welcome.

---

## Security

If you discover security issues, please report them responsibly.

---

## License

MIT
