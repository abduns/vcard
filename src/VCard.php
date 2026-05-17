<?php

namespace Dunn\VCard;

/**
 * VCard builder — RFC 6350 compliant.
 *
 * Specification: https://datatracker.ietf.org/doc/html/rfc6350
 */
class VCard
{
    /** @var array<string, mixed> */
    private array $properties = [];
    private string $version = '4.0';

    /** Map shorthand media-type aliases → full MIME type strings */
    private const MIME_MAP = [
        'JPEG' => 'image/jpeg', 'JPG'  => 'image/jpeg',
        'PNG'  => 'image/png',  'GIF'  => 'image/gif',
        'WEBP' => 'image/webp', 'SVG'  => 'image/svg+xml',
        'OGG'  => 'audio/ogg',  'MP3'  => 'audio/mpeg',
        'WAV'  => 'audio/wav',
    ];

    public function __construct(string $version = '4.0')
    {
        $this->version = $version;
    }

    public static function make(string $version = '4.0'): self
    {
        return new self($version);
    }

    // -------------------------------------------------------------------------
    // § 6.1 General Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.1
    // -------------------------------------------------------------------------

    /** SOURCE – https://datatracker.ietf.org/doc/html/rfc6350#section-6.1.3 */
    public function addSource(string $uri): self
    {
        $this->append('SOURCE', $uri);
        return $this;
    }

    /** KIND – https://datatracker.ietf.org/doc/html/rfc6350#section-6.1.4 */
    public function addKind(string $kind): self
    {
        $this->properties['KIND'] = $kind;
        return $this;
    }

    /** XML – https://datatracker.ietf.org/doc/html/rfc6350#section-6.1.5 */
    public function addXml(string $xml): self
    {
        $this->append('XML', $xml);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.2 Identification Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.2
    // -------------------------------------------------------------------------

    /** FN (required) – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.1 */
    public function addFormattedName(string $formattedName): self
    {
        $this->properties['FN'] = $this->escape($formattedName);
        return $this;
    }

    /**
     * N – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.2
     * Auto-generates FN if not already set.
     */
    public function addName(
        string $lastName,
        string $firstName,
        string $additional = '',
        string $prefix = '',
        string $suffix = ''
    ): self {
        $this->properties['N'] = implode(';', [
            $this->escape($lastName),
            $this->escape($firstName),
            $this->escape($additional),
            $this->escape($prefix),
            $this->escape($suffix),
        ]);

        if (!isset($this->properties['FN'])) {
            $parts = array_filter([$prefix, $firstName, $additional, $lastName, $suffix]);
            $this->properties['FN'] = $this->escape(trim(implode(' ', $parts)));
        }

        return $this;
    }

    /** NICKNAME – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.3 */
    public function addNickname(string ...$nicknames): self
    {
        $this->properties['NICKNAME'] = implode(',', array_map([$this, 'escape'], $nicknames));
        return $this;
    }

    /**
     * PHOTO – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.4
     * RFC 6350: value MUST be a URI. Use a data: URI for embedded images.
     * @param string $uriOrBase64 Full URI, data: URI, or raw base64 string.
     * @param string $mediaType   MIME type or shorthand (e.g. 'JPEG', 'image/png').
     */
    public function addPhoto(string $uriOrBase64, string $mediaType = 'image/jpeg'): self
    {
        $this->properties['PHOTO'] = ['value' => $uriOrBase64, 'mediaType' => $mediaType, 'prop' => 'PHOTO'];
        return $this;
    }

    /** BDAY – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.5 */
    public function addBirthday(string $date): self
    {
        $this->properties['BDAY'] = $date;
        return $this;
    }

    /** ANNIVERSARY – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.6 */
    public function addAnniversary(string $date): self
    {
        $this->properties['ANNIVERSARY'] = $date;
        return $this;
    }

    /** GENDER – https://datatracker.ietf.org/doc/html/rfc6350#section-6.2.7 */
    public function addGender(string $sex, string $identity = ''): self
    {
        $this->properties['GENDER'] = $identity !== '' ? "{$sex};{$identity}" : $sex;
        return $this;
    }

    /** GRAMGENDER – https://datatracker.ietf.org/doc/html/rfc9554#section-3.2 */
    public function addGramGender(string $gramGender): self
    {
        $this->properties['GRAMGENDER'] = strtolower($gramGender);
        return $this;
    }

    /** PRONOUNS – https://datatracker.ietf.org/doc/html/rfc9554#section-3.4 */
    public function addPronouns(string $pronouns, string $language = ''): self
    {
        $this->properties['PRONOUNS'] = ['value' => $pronouns, 'language' => $language];
        return $this;
    }

    /** LANGUAGE – https://datatracker.ietf.org/doc/html/rfc9554#section-3.3 */
    public function addLanguage(string $language): self
    {
        $this->properties['LANGUAGE'] = $language;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.3 Delivery Addressing
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.3
    // -------------------------------------------------------------------------

    /**
     * ADR – https://datatracker.ietf.org/doc/html/rfc6350#section-6.3.1
     * Components: post-office-box; extended-address; street; locality; region; postal-code; country
     */
    public function addAddress(
        string $poBox,
        string $extended,
        string $street,
        string $city,
        string $region,
        string $zip,
        string $country,
        string $type = 'WORK'
    ): self {
        $value = implode(';', [
            $this->escape($poBox),
            $this->escape($extended),
            $this->escape($street),
            $this->escape($city),
            $this->escape($region),
            $this->escape($zip),
            $this->escape($country),
        ]);
        $this->append('ADR', $value, ['type' => $type]);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.4 Communications Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.4
    // -------------------------------------------------------------------------

    /** TEL – https://datatracker.ietf.org/doc/html/rfc6350#section-6.4.1 */
    public function addPhoneNumber(string $number, string $type = 'cell'): self
    {
        $this->append('TEL', $number, ['type' => $type]);
        return $this;
    }

    /** EMAIL – https://datatracker.ietf.org/doc/html/rfc6350#section-6.4.2 */
    public function addEmail(string $email, string $type = 'work'): self
    {
        $this->append('EMAIL', $email, ['type' => $type]);
        return $this;
    }

    /** IMPP – https://datatracker.ietf.org/doc/html/rfc6350#section-6.4.3 */
    public function addImpp(string $uri, string $type = ''): self
    {
        $this->append('IMPP', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /** LANG – https://datatracker.ietf.org/doc/html/rfc6350#section-6.4.4 */
    public function addLang(string $languageTag, string $type = ''): self
    {
        $this->append('LANG', $languageTag, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /** SOCIALPROFILE – https://datatracker.ietf.org/doc/html/rfc9554#section-3.5 */
    public function addSocialProfile(string $uri, string $service = ''): self
    {
        $params = $service !== '' ? ['service-type' => $service] : [];
        $this->append('SOCIALPROFILE', $uri, $params);
        return $this;
    }

    /** CONTACT-URI – https://datatracker.ietf.org/doc/html/rfc8605#section-2.1 */
    public function addContactUri(string $uri): self
    {
        $this->append('CONTACT-URI', $uri);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.5 Geographical Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.5
    // -------------------------------------------------------------------------

    /** TZ – https://datatracker.ietf.org/doc/html/rfc6350#section-6.5.1 */
    public function addTz(string $timezone): self
    {
        $this->properties['TZ'] = $timezone;
        return $this;
    }

    /** GEO – https://datatracker.ietf.org/doc/html/rfc6350#section-6.5.2 */
    public function addGeo(string $geoUri): self
    {
        $this->properties['GEO'] = $geoUri;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.6 Organizational Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.6
    // -------------------------------------------------------------------------

    /** TITLE – https://datatracker.ietf.org/doc/html/rfc6350#section-6.6.1 */
    public function addJobTitle(string $jobTitle): self
    {
        $this->properties['TITLE'] = $this->escape($jobTitle);
        return $this;
    }

    /** ROLE – https://datatracker.ietf.org/doc/html/rfc6350#section-6.6.2 */
    public function addRole(string $role): self
    {
        $this->properties['ROLE'] = $this->escape($role);
        return $this;
    }

    /**
     * LOGO – https://datatracker.ietf.org/doc/html/rfc6350#section-6.6.3
     * RFC 6350: value MUST be a URI.
     */
    public function addLogo(string $uriOrBase64, string $mediaType = 'image/jpeg'): self
    {
        $this->properties['LOGO'] = ['value' => $uriOrBase64, 'mediaType' => $mediaType, 'prop' => 'LOGO'];
        return $this;
    }

    /** ORG – https://datatracker.ietf.org/doc/html/rfc6350#section-6.6.4 */
    public function addCompany(string $organization, string ...$units): self
    {
        $parts = [$this->escape($organization)];
        foreach ($units as $unit) {
            $parts[] = $this->escape($unit);
        }
        $this->properties['ORG'] = implode(';', $parts);
        return $this;
    }

    /** MEMBER – https://datatracker.ietf.org/doc/html/rfc6350#section-6.6.5 */
    public function addMember(string $uri): self
    {
        $this->append('MEMBER', $uri);
        return $this;
    }

    /** RELATED – https://datatracker.ietf.org/doc/html/rfc6350#section-6.6.6 */
    public function addRelated(string $uri, string $type = ''): self
    {
        $this->append('RELATED', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /** ORG-DIRECTORY – https://datatracker.ietf.org/doc/html/rfc6715#section-2.4 */
    public function addOrgDirectory(string $uri): self
    {
        $this->append('ORG-DIRECTORY', $uri);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.7 Explanatory Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.7
    // -------------------------------------------------------------------------

    /** CATEGORIES – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.1 */
    public function addCategories(string ...$categories): self
    {
        $this->properties['CATEGORIES'] = implode(',', array_map([$this, 'escape'], $categories));
        return $this;
    }

    /** NOTE – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.2 */
    public function addNote(string $note): self
    {
        $this->properties['NOTE'] = $this->escape($note);
        return $this;
    }

    /** PRODID – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.3 */
    public function addProdid(string $prodid): self
    {
        $this->properties['PRODID'] = $prodid;
        return $this;
    }

    /**
     * SOUND – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.5
     * RFC 6350: value MUST be a URI.
     */
    public function addSound(string $uriOrBase64, string $mediaType = 'audio/ogg'): self
    {
        $this->properties['SOUND'] = ['value' => $uriOrBase64, 'mediaType' => $mediaType, 'prop' => 'SOUND'];
        return $this;
    }

    /** UID – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.6 */
    public function addUid(string $uid): self
    {
        $this->properties['UID'] = $uid;
        return $this;
    }

    /** CLIENTPIDMAP – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.7 */
    public function addClientpidmap(int $pid, string $uri): self
    {
        $this->append('CLIENTPIDMAP', "{$pid};{$uri}");
        return $this;
    }

    /** URL – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.8 */
    public function addUrl(string $url, string $type = 'work'): self
    {
        $this->append('URL', $url, ['type' => $type]);
        return $this;
    }

    /** CREATED – https://datatracker.ietf.org/doc/html/rfc9554#section-3.1 */
    public function addCreated(string $timestamp): self
    {
        $this->properties['CREATED'] = $timestamp;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.8 Security Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.8
    // -------------------------------------------------------------------------

    /**
     * KEY – https://datatracker.ietf.org/doc/html/rfc6350#section-6.8.1
     * RFC 6350: value MUST be a URI.
     */
    public function addKey(string $uriOrBase64, string $mediaType = 'application/pgp-keys'): self
    {
        $this->properties['KEY'] = ['value' => $uriOrBase64, 'mediaType' => $mediaType, 'prop' => 'KEY'];
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.9 Calendar Properties
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.9
    // -------------------------------------------------------------------------

    /** FBURL – https://datatracker.ietf.org/doc/html/rfc6350#section-6.9.1 */
    public function addFburl(string $uri, string $type = ''): self
    {
        $this->append('FBURL', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /** CALADRURI – https://datatracker.ietf.org/doc/html/rfc6350#section-6.9.2 */
    public function addCaladruri(string $uri, string $type = ''): self
    {
        $this->append('CALADRURI', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /** CALURI – https://datatracker.ietf.org/doc/html/rfc6350#section-6.9.3 */
    public function addCaluri(string $uri, string $type = ''): self
    {
        $this->append('CALURI', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    // -------------------------------------------------------------------------
    // RFC 6474 – Birth/Death extension
    // https://datatracker.ietf.org/doc/html/rfc6474
    // -------------------------------------------------------------------------

    /** BIRTHPLACE – https://datatracker.ietf.org/doc/html/rfc6474#section-2.1 */
    public function addBirthplace(string $place): self
    {
        $this->properties['BIRTHPLACE'] = $this->escape($place);
        return $this;
    }

    /** DEATHPLACE – https://datatracker.ietf.org/doc/html/rfc6474#section-2.2 */
    public function addDeathplace(string $place): self
    {
        $this->properties['DEATHPLACE'] = $this->escape($place);
        return $this;
    }

    /** DEATHDATE – https://datatracker.ietf.org/doc/html/rfc6474#section-2.3 */
    public function addDeathdate(string $date): self
    {
        $this->properties['DEATHDATE'] = $date;
        return $this;
    }

    // -------------------------------------------------------------------------
    // RFC 6715 – Expertise / Hobby / Interest
    // https://datatracker.ietf.org/doc/html/rfc6715
    // -------------------------------------------------------------------------

    /** EXPERTISE – https://datatracker.ietf.org/doc/html/rfc6715#section-2.1 */
    public function addExpertise(string $area, string $level = 'average'): self
    {
        $this->append('EXPERTISE', $this->escape($area), ['level' => $level]);
        return $this;
    }

    /** HOBBY – https://datatracker.ietf.org/doc/html/rfc6715#section-2.2 */
    public function addHobby(string $hobby, string $level = 'average'): self
    {
        $this->append('HOBBY', $this->escape($hobby), ['level' => $level]);
        return $this;
    }

    /** INTEREST – https://datatracker.ietf.org/doc/html/rfc6715#section-2.3 */
    public function addInterest(string $interest, string $level = 'average'): self
    {
        $this->append('INTEREST', $this->escape($interest), ['level' => $level]);
        return $this;
    }

    // -------------------------------------------------------------------------
    // RFC 9555 – JSContact/vCard bridge
    // https://datatracker.ietf.org/doc/html/rfc9555
    // -------------------------------------------------------------------------

    /** JSPROP – https://datatracker.ietf.org/doc/html/rfc9555#section-3.2.1 */
    public function addJsprop(string $key, string $jsonValue): self
    {
        $this->append('JSPROP', $jsonValue, ['jsptr' => $key]);
        return $this;
    }

    // -------------------------------------------------------------------------
    // Generic escape hatch
    // https://datatracker.ietf.org/doc/html/rfc6350#section-6.10
    // -------------------------------------------------------------------------

    /**
     * Add a custom or vendor-prefixed property (X- prefix recommended).
     *
     * @param array<string, mixed> $params
     */
    public function addProperty(string $name, string $value, array $params = []): self
    {
        $this->append($name, $value, $params);
        return $this;
    }

    // -------------------------------------------------------------------------
    // Build
    // -------------------------------------------------------------------------

    /**
     * Build and return the vCard string.
     *
     * @throws \InvalidArgumentException if FN is missing (required by RFC 6350 §6.2.1)
     */
    public function build(): string
    {
        if (!isset($this->properties['FN'])) {
            throw new \InvalidArgumentException(
                'A formatted name (FN) is required by RFC 6350 §6.2.1. ' .
                'Call addName() or addFormattedName() before build().'
            );
        }

        $lines = [
            'BEGIN:VCARD',
            'VERSION:' . $this->version,
        ];

        foreach ($this->properties as $key => $data) {
            if (is_array($data)) {
                // Multi-value list (TEL, EMAIL, ADR, URL, SOCIALPROFILE …)
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data as $item) {
                        $lines[] = $this->buildLine($key, $item);
                    }
                } else {
                    // Single structured value (PHOTO, LOGO, SOUND, KEY, PRONOUNS)
                    $lines[] = $this->buildLine($key, $data);
                }
            } else {
                $lines[] = $key . ':' . $data;
            }
        }

        // REV – https://datatracker.ietf.org/doc/html/rfc6350#section-6.7.4
        $lines[] = 'REV:' . gmdate('Ymd\THis\Z');
        $lines[] = 'END:VCARD';

        // Apply line folding per RFC 6350 §3.2
        $folded = array_map([$this, 'fold'], $lines);

        return implode("\r\n", $folded) . "\r\n";
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** @param array<string, mixed> $params */
    private function append(string $key, string $value, array $params = []): void
    {
        if (!isset($this->properties[$key])) {
            $this->properties[$key] = [];
        }
        $this->properties[$key][] = array_merge(['value' => $value], $params);
    }

    /** @param array<string, mixed> $data */
    private function buildLine(string $key, array $data): string
    {
        $value  = $data['value'] ?? '';
        $params = [];

        // TYPE parameter
        if (!empty($data['type'])) {
            $params[] = 'TYPE=' . $data['type'];
        }

        // Media-type properties: PHOTO, LOGO, SOUND, KEY
        // RFC 6350: value MUST be a URI → use data: URI for embedded binary.
        if (!empty($data['mediaType'])) {
            $mime = self::MIME_MAP[strtoupper($data['mediaType'])] ?? $data['mediaType'];

            if (str_starts_with($value, 'http') || str_starts_with($value, 'ftp') || str_starts_with($value, 'data:')) {
                // Already a URI – optionally advertise media type
                if (!str_starts_with($value, 'data:')) {
                    $params[] = 'MEDIATYPE=' . $mime;
                }
            } else {
                // Raw base64 → wrap as data: URI (RFC 6350 compliant)
                $value = 'data:' . $mime . ';base64,' . $value;
            }
        }

        // LANGUAGE parameter (PRONOUNS)
        if (!empty($data['language'])) {
            $params[] = 'LANGUAGE=' . $data['language'];
        }

        // LEVEL parameter (EXPERTISE, HOBBY, INTEREST)
        if (!empty($data['level'])) {
            $params[] = 'LEVEL=' . $data['level'];
        }

        // SERVICE-TYPE parameter (SOCIALPROFILE)
        if (!empty($data['service-type'])) {
            $params[] = 'SERVICE-TYPE=' . $data['service-type'];
        }

        // JSPTR parameter (JSPROP)
        if (!empty($data['jsptr'])) {
            $params[] = 'JSPTR=' . $data['jsptr'];
        }

        $paramStr = !empty($params) ? ';' . implode(';', $params) : '';

        return $key . $paramStr . ':' . $value;
    }

    /**
     * Fold long lines per RFC 6350 §3.2.
     * Lines SHOULD be folded at 75 octets (bytes), excluding the CRLF.
     * Continuation lines begin with a single SPACE.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6350#section-3.2
     */
    private function fold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $output = '';
        // First chunk: 75 bytes
        $output .= substr($line, 0, 75);
        $line    = substr($line, 75);

        // Subsequent chunks: 74 bytes (1 byte reserved for leading SPACE)
        while (strlen($line) > 0) {
            $output .= "\r\n " . substr($line, 0, 74);
            $line    = substr($line, 74);
        }

        return $output;
    }

    /**
     * Escape special characters per RFC 6350 §3.4.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6350#section-3.4
     */
    private function escape(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace("\r\n", "\n", $value);
        $value = str_replace("\n", '\n', $value);
        return $value;
    }
}
