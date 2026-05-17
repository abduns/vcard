# vCard

Modern, framework-agnostic PHP library for generating vCard data with full RFC 6350 (vCard 4.0) support.

[![Tests](https://github.com/abduns/vcard/actions/workflows/tests.yml/badge.svg)](https://github.com/abduns/vcard/actions)
[![Latest Version](https://img.shields.io/github/v/tag/abduns/vcard?label=version)](https://github.com/abduns/vcard/tags)
[![License](https://img.shields.io/github/license/abduns/vcard)](LICENSE.md)

## Features

- RFC 6350 (vCard 4.0) compliant
- Line folding at 75 octets (§3.2)
- UTF-8 support
- Generate `.vcf` files
- All 50 IANA registered properties supported
- `data:` URI encoding for embedded binary (photos, logos, sounds)
- Custom and vendor-prefixed property support
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

Generated output:

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

## Standards

Designed around official Internet standards and IANA registries.

### Supported Specifications

| RFC | Description |
|-----|-------------|
| [RFC 6350](https://datatracker.ietf.org/doc/html/rfc6350) | vCard Format Specification (vCard 4.0) — core standard |
| [RFC 6474](https://datatracker.ietf.org/doc/html/rfc6474) | Birth/Death properties |
| [RFC 6715](https://datatracker.ietf.org/doc/html/rfc6715) | Expertise, Hobby, Interest properties |
| [RFC 8605](https://datatracker.ietf.org/doc/html/rfc8605) | CONTACT-URI property |
| [RFC 9554](https://datatracker.ietf.org/doc/html/rfc9554) | GRAMGENDER, LANGUAGE, PRONOUNS, SOCIALPROFILE, CREATED |
| [RFC 9555](https://datatracker.ietf.org/doc/html/rfc9555) | JSPROP (JSContact bridge) |

Registry: https://www.iana.org/assignments/vcard-elements/vcard-elements.xhtml

---

## Supported Properties

### General
| Property | Method | RFC |
|----------|--------|-----|
| `SOURCE` | `addSource()` | RFC 6350 §6.1.3 |
| `KIND` | `addKind()` | RFC 6350 §6.1.4 |
| `XML` | `addXml()` | RFC 6350 §6.1.5 |

### Identification
| Property | Method | RFC |
|----------|--------|-----|
| `FN` ⚠️ required | `addFormattedName()` | RFC 6350 §6.2.1 |
| `N` | `addName()` | RFC 6350 §6.2.2 |
| `NICKNAME` | `addNickname()` | RFC 6350 §6.2.3 |
| `PHOTO` | `addPhoto()` | RFC 6350 §6.2.4 |
| `BDAY` | `addBirthday()` | RFC 6350 §6.2.5 |
| `ANNIVERSARY` | `addAnniversary()` | RFC 6350 §6.2.6 |
| `GENDER` | `addGender()` | RFC 6350 §6.2.7 |
| `GRAMGENDER` | `addGramGender()` | RFC 9554 §3.2 |
| `PRONOUNS` | `addPronouns()` | RFC 9554 §3.4 |
| `LANGUAGE` | `addLanguage()` | RFC 9554 §3.3 |

### Delivery Addressing
| Property | Method | RFC |
|----------|--------|-----|
| `ADR` | `addAddress()` | RFC 6350 §6.3.1 |

### Communications
| Property | Method | RFC |
|----------|--------|-----|
| `TEL` | `addPhoneNumber()` | RFC 6350 §6.4.1 |
| `EMAIL` | `addEmail()` | RFC 6350 §6.4.2 |
| `IMPP` | `addImpp()` | RFC 6350 §6.4.3 |
| `LANG` | `addLang()` | RFC 6350 §6.4.4 |
| `SOCIALPROFILE` | `addSocialProfile()` | RFC 9554 §3.5 |
| `CONTACT-URI` | `addContactUri()` | RFC 8605 §2.1 |

### Geographical
| Property | Method | RFC |
|----------|--------|-----|
| `TZ` | `addTz()` | RFC 6350 §6.5.1 |
| `GEO` | `addGeo()` | RFC 6350 §6.5.2 |

### Organizational
| Property | Method | RFC |
|----------|--------|-----|
| `TITLE` | `addJobTitle()` | RFC 6350 §6.6.1 |
| `ROLE` | `addRole()` | RFC 6350 §6.6.2 |
| `LOGO` | `addLogo()` | RFC 6350 §6.6.3 |
| `ORG` | `addCompany()` | RFC 6350 §6.6.4 |
| `MEMBER` | `addMember()` | RFC 6350 §6.6.5 |
| `RELATED` | `addRelated()` | RFC 6350 §6.6.6 |
| `ORG-DIRECTORY` | `addOrgDirectory()` | RFC 6715 §2.4 |

### Explanatory
| Property | Method | RFC |
|----------|--------|-----|
| `CATEGORIES` | `addCategories()` | RFC 6350 §6.7.1 |
| `NOTE` | `addNote()` | RFC 6350 §6.7.2 |
| `PRODID` | `addProdid()` | RFC 6350 §6.7.3 |
| `SOUND` | `addSound()` | RFC 6350 §6.7.5 |
| `UID` | `addUid()` | RFC 6350 §6.7.6 |
| `CLIENTPIDMAP` | `addClientpidmap()` | RFC 6350 §6.7.7 |
| `URL` | `addUrl()` | RFC 6350 §6.7.8 |
| `CREATED` | `addCreated()` | RFC 9554 §3.1 |

### Security
| Property | Method | RFC |
|----------|--------|-----|
| `KEY` | `addKey()` | RFC 6350 §6.8.1 |

### Calendar
| Property | Method | RFC |
|----------|--------|-----|
| `FBURL` | `addFburl()` | RFC 6350 §6.9.1 |
| `CALADRURI` | `addCaladruri()` | RFC 6350 §6.9.2 |
| `CALURI` | `addCaluri()` | RFC 6350 §6.9.3 |

### Birth/Death (RFC 6474)
| Property | Method |
|----------|--------|
| `BIRTHPLACE` | `addBirthplace()` |
| `DEATHPLACE` | `addDeathplace()` |
| `DEATHDATE` | `addDeathdate()` |

### Expertise / Hobby / Interest (RFC 6715)
| Property | Method |
|----------|--------|
| `EXPERTISE` | `addExpertise()` |
| `HOBBY` | `addHobby()` |
| `INTEREST` | `addInterest()` |

### JSContact (RFC 9555)
| Property | Method |
|----------|--------|
| `JSPROP` | `addJsprop()` |

---

## Usage

### Name

```php
VCard::make()->addName(
    lastName:   'Doe',
    firstName:  'John',
    additional: '',
    prefix:     'Dr.',
    suffix:     'Jr.'
);
```

### Email

```php
->addEmail('john@example.com', 'work')
->addEmail('john@personal.com', 'home')
```

### Phone

```php
->addPhoneNumber('+62-812-3456-7890', 'cell')
->addPhoneNumber('+62-21-0000-0000', 'work')
```

### Address

```php
->addAddress(
    poBox:    '',
    extended: '',
    street:   'Jl. Example No. 1',
    city:     'Bandung',
    region:   'West Java',
    zip:      '40000',
    country:  'Indonesia',
    type:     'work'
)
```

### Photo

```php
// URL
->addPhoto('https://example.com/photo.jpg', 'image/jpeg')

// Embedded (raw base64 → automatically wrapped as data: URI)
->addPhoto($base64String, 'image/jpeg')
```

### Social Profiles

```php
->addSocialProfile('https://github.com/johndoe', 'GitHub')
->addSocialProfile('https://linkedin.com/in/johndoe', 'LinkedIn')
```

### Expertise / Hobby / Interest

```php
->addExpertise('PHP', 'expert')
->addHobby('Rock Climbing', 'average')
->addInterest('Open Source', 'expert')
```

### Categories

```php
->addCategories('Developer', 'Open Source', 'PHP')
```

### UID (recommended for sync)

```php
->addUid('urn:uuid:' . $uuid)
```

### Custom Properties

```php
->addProperty('X-CUSTOM-FIELD', 'value')
```

---

## Validation

The only enforced requirement at runtime is `FN` (Formatted Name), which is mandatory per [RFC 6350 §6.2.1](https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.1).

```php
// Throws InvalidArgumentException
VCard::make()->addPhoneNumber('123')->build();

// OK — addName() sets FN automatically
VCard::make()->addName('Doe', 'John')->build();
```

All other values are passed through as-is for maximum flexibility.

---

## Compatibility

This package aims to work with:

- Apple Contacts
- Google Contacts
- Android Contacts
- Outlook
- Thunderbird
- CardDAV systems

Compatibility may vary depending on vendor-specific behavior.

---

## Design Goals

- Standards-oriented (RFC 6350 first)
- Zero external dependencies
- Modern PHP 8.2+ API
- Interoperability first
- Extensible and lightweight

---

## Roadmap

- [ ] Validation API
- [ ] vCard parsing / reading
- [ ] jCard (JSON) support
- [ ] xCard (XML) support
- [ ] CardDAV helpers

---

## Why Another vCard Library?

Most existing PHP vCard libraries are:

- Focused on vCard 2.1 or 3.0
- No longer actively maintained
- Not aligned with RFC 6350
- Using outdated binary encoding (`ENCODING=b`) instead of `data:` URIs

This package focuses on modern vCard 4.0 standards and a clean developer experience.

---

## Contributing

Contributions, bug reports, and interoperability test cases are welcome.

---

## License

MIT — see [LICENSE.md](LICENSE.md)
