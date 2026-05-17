# VCard Generator for PHP

A lightweight, framework-agnostic VCard 3.0/4.0 generator for PHP 8.2+ with zero dependencies.

Fully compliant with the [IANA vCard Elements registry](https://www.iana.org/assignments/vcard-elements/vcard-elements.xhtml), implementing all 50 standard properties across RFC 6350, RFC 6474, RFC 6715, RFC 8605, RFC 9554, and RFC 9555.

## Requirements

- PHP 8.2 or higher

## Installation

```bash
composer require abduns/vcard
```

## Quick Start

```php
use Dunn\VCard\VCard;

$vcf = VCard::make()
    ->addName('Doe', 'John', prefix: 'Mr.')
    ->addEmail('john.doe@example.com', 'WORK')
    ->addPhoneNumber('+1234567890', 'CELL')
    ->addUrl('https://johndoe.com')
    ->build();

// Save to file
file_put_contents('john_doe.vcf', $vcf);
```

> **Note:** `FN` (Formatted Name) is the **only required property** per RFC 6350. Calling `addName()` sets it automatically. If you skip both `addName()` and `addFormattedName()`, `build()` will throw an `InvalidArgumentException`.

---

## All Available Methods

### General Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addSource(string $uri)` | `SOURCE` | RFC 6350 §6.1.3 |
| `addKind(string $kind)` | `KIND` | RFC 6350 §6.1.4 |
| `addXml(string $xml)` | `XML` | RFC 6350 §6.1.5 |

### Identification Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addFormattedName(string $name)` | `FN` ⚠️ required | RFC 6350 §6.2.1 |
| `addName(string $last, string $first, ...)` | `N` | RFC 6350 §6.2.2 |
| `addNickname(string ...$names)` | `NICKNAME` | RFC 6350 §6.2.3 |
| `addPhoto(string $urlOrBase64, string $mediaType)` | `PHOTO` | RFC 6350 §6.2.4 |
| `addBirthday(string $date)` | `BDAY` | RFC 6350 §6.2.5 |
| `addAnniversary(string $date)` | `ANNIVERSARY` | RFC 6350 §6.2.6 |
| `addGender(string $sex, string $identity)` | `GENDER` | RFC 6350 §6.2.7 |
| `addGramGender(string $gramGender)` | `GRAMGENDER` | RFC 9554 §3.2 |
| `addPronouns(string $pronouns, string $lang)` | `PRONOUNS` | RFC 9554 §3.4 |
| `addLanguage(string $language)` | `LANGUAGE` | RFC 9554 §3.3 |

### Delivery Addressing Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addAddress(string $poBox, string $ext, string $street, string $city, string $region, string $zip, string $country, string $type)` | `ADR` | RFC 6350 §6.3.1 |

### Communications Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addPhoneNumber(string $number, string $type)` | `TEL` | RFC 6350 §6.4.1 |
| `addEmail(string $email, string $type)` | `EMAIL` | RFC 6350 §6.4.2 |
| `addImpp(string $uri, string $type)` | `IMPP` | RFC 6350 §6.4.3 |
| `addLang(string $languageTag, string $type)` | `LANG` | RFC 6350 §6.4.4 |
| `addSocialProfile(string $uri, string $service)` | `SOCIALPROFILE` | RFC 9554 §3.5 |
| `addContactUri(string $uri)` | `CONTACT-URI` | RFC 8605 §2.1 |

### Geographical Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addTz(string $timezone)` | `TZ` | RFC 6350 §6.5.1 |
| `addGeo(string $geoUri)` | `GEO` | RFC 6350 §6.5.2 |

### Organizational Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addJobTitle(string $title)` | `TITLE` | RFC 6350 §6.6.1 |
| `addRole(string $role)` | `ROLE` | RFC 6350 §6.6.2 |
| `addLogo(string $urlOrBase64, string $mediaType)` | `LOGO` | RFC 6350 §6.6.3 |
| `addCompany(string $org, string ...$units)` | `ORG` | RFC 6350 §6.6.4 |
| `addMember(string $uri)` | `MEMBER` | RFC 6350 §6.6.5 |
| `addRelated(string $uri, string $type)` | `RELATED` | RFC 6350 §6.6.6 |
| `addOrgDirectory(string $uri)` | `ORG-DIRECTORY` | RFC 6715 §2.4 |

### Explanatory Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addCategories(string ...$tags)` | `CATEGORIES` | RFC 6350 §6.7.1 |
| `addNote(string $note)` | `NOTE` | RFC 6350 §6.7.2 |
| `addProdid(string $prodid)` | `PRODID` | RFC 6350 §6.7.3 |
| `addSound(string $urlOrBase64, string $mediaType)` | `SOUND` | RFC 6350 §6.7.5 |
| `addUid(string $uid)` | `UID` | RFC 6350 §6.7.6 |
| `addClientpidmap(int $pid, string $uri)` | `CLIENTPIDMAP` | RFC 6350 §6.7.7 |
| `addUrl(string $url, string $type)` | `URL` | RFC 6350 §6.7.8 |
| `addCreated(string $timestamp)` | `CREATED` | RFC 9554 §3.1 |

### Security Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addKey(string $key, string $mediaType)` | `KEY` | RFC 6350 §6.8.1 |

### Calendar Properties
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addFburl(string $uri, string $type)` | `FBURL` | RFC 6350 §6.9.1 |
| `addCaladruri(string $uri, string $type)` | `CALADRURI` | RFC 6350 §6.9.2 |
| `addCaluri(string $uri, string $type)` | `CALURI` | RFC 6350 §6.9.3 |

### Birth/Death Properties (RFC 6474)
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addBirthplace(string $place)` | `BIRTHPLACE` | RFC 6474 §2.1 |
| `addDeathplace(string $place)` | `DEATHPLACE` | RFC 6474 §2.2 |
| `addDeathdate(string $date)` | `DEATHDATE` | RFC 6474 §2.3 |

### Expertise / Hobby / Interest Properties (RFC 6715)
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addExpertise(string $area, string $level)` | `EXPERTISE` | RFC 6715 §2.1 |
| `addHobby(string $hobby, string $level)` | `HOBBY` | RFC 6715 §2.2 |
| `addInterest(string $interest, string $level)` | `INTEREST` | RFC 6715 §2.3 |

### JSContact Properties (RFC 9555)
| Method | VCard Property | RFC |
|--------|---------------|-----|
| `addJsprop(string $key, string $jsonValue)` | `JSPROP` | RFC 9555 §3.2.1 |

### Custom / Vendor-Prefixed Properties
| Method | Description |
|--------|-------------|
| `addProperty(string $name, string $value, array $params)` | Add any `X-` or vendor property |

---

## Best Practices

### ✅ 1. Always use `addName()` — not just `addFormattedName()`

`addName()` sets both `N` (structured name components) and auto-generates `FN`. This gives clients (iOS Contacts, Google Contacts, Outlook) the data they need to sort and search correctly.

```php
// ✅ Good — sets N + FN automatically
VCard::make()->addName('Doe', 'John', prefix: 'Dr.');

// ⚠️ Acceptable — only sets FN, no structured name for sorting
VCard::make()->addFormattedName('Dr. John Doe');
```

### ✅ 2. Always set `UID` for addressbook sync

Without `UID`, many sync clients (CardDAV, Exchange) cannot update existing entries. Use a UUID:

```php
VCard::make()
    ->addName('Doe', 'John')
    ->addUid('urn:uuid:' . \Ramsey\Uuid\Uuid::uuid4());
```

### ✅ 3. Use `CELL` for mobile numbers

The `CELL` type ensures iOS/Android correctly identifies numbers for messaging apps:

```php
->addPhoneNumber('+1234567890', 'CELL')  // ✅ Mobile
->addPhoneNumber('+0987654321', 'WORK')  // Office
->addPhoneNumber('+1122334455', 'FAX')   // Fax
```

### ✅ 4. Use `geo:` URIs for GEO

```php
->addGeo('geo:37.386013,-122.082932')
```

### ✅ 5. Prefer VCard 3.0 for maximum device compatibility

VCard 4.0 is the latest standard, but many phones and clients still have incomplete support. Unless you specifically need 4.0 features (like `JSPROP`, `GRAMGENDER`, `PRONOUNS`), stick with 3.0:

```php
VCard::make('3.0') // default — best compatibility
VCard::make('4.0') // use only if you need RFC 9554/9555 features
```

### ✅ 6. Use `SOCIALPROFILE` for social links (not custom `X-` properties)

RFC 9554 defines a proper `SOCIALPROFILE` property. Prefer it over legacy `X-TWITTER`, `X-LINKEDIN` etc.:

```php
// ✅ Modern — RFC 9554
->addSocialProfile('https://github.com/johndoe', 'GitHub')
->addSocialProfile('https://linkedin.com/in/johndoe', 'LinkedIn')

// ⚠️ Legacy — only for clients that don't understand SOCIALPROFILE
->addProperty('X-TWITTER', '@johndoe')
```

### ✅ 7. Prefix custom properties with `X-`

Any non-standard property must be prefixed with `X-` to avoid conflicts with future RFC additions:

```php
->addProperty('X-MY-APP-ID', 'user_12345')
```

---

## Validation

The only hard requirement enforced at runtime is that `FN` (Formatted Name) **must** be set before calling `build()`. This is the single mandatory property per RFC 6350 §6.2.1.

All other values are passed through as-is, giving you full flexibility to handle non-standard data, edge cases, and proprietary client extensions.

---

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
