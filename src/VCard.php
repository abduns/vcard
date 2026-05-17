<?php

namespace Dunn\VCard;

class VCard
{
    private array $properties = [];
    private string $version = '3.0';

    public function __construct(string $version = '3.0')
    {
        $this->version = $version;
    }

    public static function make(string $version = '3.0'): self
    {
        return new self($version);
    }

    // -------------------------------------------------------------------------
    // § 6.1 General Properties
    // -------------------------------------------------------------------------

    /**
     * SOURCE – URI(s) that can be used to get the latest version of this vCard.
     * [RFC6350, Section 6.1.3]
     */
    public function addSource(string $uri): self
    {
        $this->append('SOURCE', $uri);
        return $this;
    }

    /**
     * KIND – The type of entity this vCard represents.
     * Values: individual | group | org | location
     * [RFC6350, Section 6.1.4]
     */
    public function addKind(string $kind): self
    {
        $this->properties['KIND'] = $kind;
        return $this;
    }

    /**
     * XML – Any XML not covered by other vCard properties.
     * [RFC6350, Section 6.1.5]
     */
    public function addXml(string $xml): self
    {
        $this->append('XML', $xml);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.2 Identification Properties
    // -------------------------------------------------------------------------

    /**
     * FN – The formatted name string associated with the vCard.
     * [RFC6350, Section 6.2.1]
     */
    public function addFormattedName(string $formattedName): self
    {
        $this->properties['FN'] = $formattedName;
        return $this;
    }

    /**
     * N – Components of the name of the object the vCard represents.
     * Auto-generates FN if not already set.
     * [RFC6350, Section 6.2.2]
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

    /**
     * NICKNAME – One or more descriptive/familiar names for the object.
     * [RFC6350, Section 6.2.3]
     */
    public function addNickname(string ...$nicknames): self
    {
        $this->properties['NICKNAME'] = implode(',', array_map([$this, 'escape'], $nicknames));
        return $this;
    }

    /**
     * PHOTO – An image or photograph information that annotates some aspect of
     * the object the vCard represents.
     * [RFC6350, Section 6.2.4]
     */
    public function addPhoto(string $urlOrBase64, string $mediaType = 'JPEG'): self
    {
        $this->properties['PHOTO'] = ['value' => $urlOrBase64, 'mediaType' => $mediaType];
        return $this;
    }

    /**
     * BDAY – The birth date of the object the vCard represents.
     * [RFC6350, Section 6.2.5]
     */
    public function addBirthday(string $date): self
    {
        $this->properties['BDAY'] = $date;
        return $this;
    }

    /**
     * ANNIVERSARY – The date of marriage, or equivalent, of the object.
     * [RFC6350, Section 6.2.6]
     */
    public function addAnniversary(string $date): self
    {
        $this->properties['ANNIVERSARY'] = $date;
        return $this;
    }

    /**
     * GENDER – Defines the sex and/or gender identity of the object.
     * sex: M | F | O | N | U
     * [RFC6350, Section 6.2.7]
     */
    public function addGender(string $sex, string $identity = ''): self
    {
        $this->properties['GENDER'] = $identity !== '' ? "{$sex};{$identity}" : $sex;
        return $this;
    }

    /**
     * GRAMGENDER – Defines the grammatical gender to be used for the object.
     * Values: animate | common | feminine | inanimate | masculine | neuter
     * [RFC9554, Section 3.2]
     */
    public function addGramGender(string $gramGender): self
    {
        $this->properties['GRAMGENDER'] = strtolower($gramGender);
        return $this;
    }

    /**
     * PRONOUNS – Defines the pronouns to be used for the object.
     * [RFC9554, Section 3.4]
     */
    public function addPronouns(string $pronouns, string $language = ''): self
    {
        $this->properties['PRONOUNS'] = ['value' => $pronouns, 'language' => $language];
        return $this;
    }

    /**
     * LANGUAGE – Defines the default language for the vCard.
     * [RFC9554, Section 3.3]
     */
    public function addLanguage(string $language): self
    {
        $this->properties['LANGUAGE'] = $language;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.3 Delivery Addressing Properties
    // -------------------------------------------------------------------------

    /**
     * ADR – A structured representation of the physical delivery address.
     * [RFC6350, Section 6.3.1]
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
    // -------------------------------------------------------------------------

    /**
     * TEL – The canonical number string for a telephone number.
     * [RFC6350, Section 6.4.1]
     */
    public function addPhoneNumber(string $number, string $type = 'CELL'): self
    {
        $this->append('TEL', $number, ['type' => $type]);
        return $this;
    }

    /**
     * EMAIL – The address for electronic mail communication.
     * [RFC6350, Section 6.4.2]
     */
    public function addEmail(string $email, string $type = 'INTERNET'): self
    {
        $this->append('EMAIL', $email, ['type' => $type]);
        return $this;
    }

    /**
     * IMPP – An URI for an instant messaging and presence protocol.
     * e.g. "xmpp:user@host", "sip:user@host", "aim:screenname"
     * [RFC6350, Section 6.4.3]
     */
    public function addImpp(string $uri, string $type = ''): self
    {
        $this->append('IMPP', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /**
     * LANG – Defines the language(s) that may be used to contact the object.
     * [RFC6350, Section 6.4.4]
     */
    public function addLang(string $languageTag, string $type = ''): self
    {
        $this->append('LANG', $languageTag, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /**
     * SOCIALPROFILE – Specifies a social network resource for the object.
     * [RFC9554, Section 3.5]
     */
    public function addSocialProfile(string $uri, string $service = ''): self
    {
        $params = $service !== '' ? ['service-type' => $service] : [];
        $this->append('SOCIALPROFILE', $uri, $params);
        return $this;
    }

    /**
     * CONTACT-URI – A URI to be used in addition to the vCard for contacting the object.
     * [RFC8605, Section 2.1]
     */
    public function addContactUri(string $uri): self
    {
        $this->append('CONTACT-URI', $uri);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.5 Geographical Properties
    // -------------------------------------------------------------------------

    /**
     * TZ – The time zone(s) of the object the vCard represents.
     * [RFC6350, Section 6.5.1]
     */
    public function addTz(string $timezone): self
    {
        $this->properties['TZ'] = $timezone;
        return $this;
    }

    /**
     * GEO – A geographic position associated with the object.
     * Formatted as a "geo:" URI, e.g. "geo:37.386013,-122.082932"
     * [RFC6350, Section 6.5.2]
     */
    public function addGeo(string $geoUri): self
    {
        $this->properties['GEO'] = $geoUri;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.6 Organizational Properties
    // -------------------------------------------------------------------------

    /**
     * TITLE – The position or job of the object.
     * [RFC6350, Section 6.6.1]
     */
    public function addJobTitle(string $jobTitle): self
    {
        $this->properties['TITLE'] = $this->escape($jobTitle);
        return $this;
    }

    /**
     * ROLE – The role, occupation, or business category of the object.
     * [RFC6350, Section 6.6.2]
     */
    public function addRole(string $role): self
    {
        $this->properties['ROLE'] = $this->escape($role);
        return $this;
    }

    /**
     * LOGO – A graphic image of a logo associated with the object.
     * [RFC6350, Section 6.6.3]
     */
    public function addLogo(string $urlOrBase64, string $mediaType = 'JPEG'): self
    {
        $this->properties['LOGO'] = ['value' => $urlOrBase64, 'mediaType' => $mediaType];
        return $this;
    }

    /**
     * ORG – The name and optionally the unit(s) of the organization.
     * Pass multiple units as additional arguments.
     * [RFC6350, Section 6.6.4]
     */
    public function addCompany(string $organization, string ...$units): self
    {
        $parts = [$this->escape($organization)];
        foreach ($units as $unit) {
            $parts[] = $this->escape($unit);
        }
        $this->properties['ORG'] = implode(';', $parts);
        return $this;
    }

    /**
     * MEMBER – A member in the group this vCard represents.
     * VALUE must be a URI (e.g. "mailto:user@example.com", "urn:uuid:...").
     * [RFC6350, Section 6.6.5]
     */
    public function addMember(string $uri): self
    {
        $this->append('MEMBER', $uri);
        return $this;
    }

    /**
     * RELATED – A person or entity that is related to the object.
     * [RFC6350, Section 6.6.6]
     */
    public function addRelated(string $uri, string $type = ''): self
    {
        $this->append('RELATED', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /**
     * ORG-DIRECTORY – A URI representing the object's directory entries.
     * [RFC6715, Section 2.4]
     */
    public function addOrgDirectory(string $uri): self
    {
        $this->append('ORG-DIRECTORY', $uri);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.7 Explanatory Properties
    // -------------------------------------------------------------------------

    /**
     * CATEGORIES – A list of "tags" that can be used to describe the object.
     * [RFC6350, Section 6.7.1]
     */
    public function addCategories(string ...$categories): self
    {
        $escaped = array_map([$this, 'escape'], $categories);
        $this->properties['CATEGORIES'] = implode(',', $escaped);
        return $this;
    }

    /**
     * NOTE – Supplemental information or a comment associated with the vCard.
     * [RFC6350, Section 6.7.2]
     */
    public function addNote(string $note): self
    {
        $this->properties['NOTE'] = $this->escape($note);
        return $this;
    }

    /**
     * PRODID – The identifier for the product that created the vCard object.
     * [RFC6350, Section 6.7.3]
     */
    public function addProdid(string $prodid): self
    {
        $this->properties['PRODID'] = $prodid;
        return $this;
    }

    /**
     * SOUND – A digital sound content associated with the object.
     * [RFC6350, Section 6.7.5]
     */
    public function addSound(string $urlOrBase64, string $mediaType = 'OGG'): self
    {
        $this->properties['SOUND'] = ['value' => $urlOrBase64, 'mediaType' => $mediaType];
        return $this;
    }

    /**
     * UID – A globally unique identifier for the vCard.
     * [RFC6350, Section 6.7.6]
     */
    public function addUid(string $uid): self
    {
        $this->properties['UID'] = $uid;
        return $this;
    }

    /**
     * CLIENTPIDMAP – A PID source identifier mapping.
     * [RFC6350, Section 6.7.7]
     */
    public function addClientpidmap(int $pid, string $uri): self
    {
        $this->append('CLIENTPIDMAP', "{$pid};{$uri}");
        return $this;
    }

    /**
     * URL – A URI pointing to a website associated with the object.
     * [RFC6350, Section 6.7.8]
     */
    public function addUrl(string $url, string $type = 'WORK'): self
    {
        $this->append('URL', $url, ['type' => $type]);
        return $this;
    }

    /**
     * CREATED – The timestamp when this vCard was created.
     * [RFC9554, Section 3.1]
     */
    public function addCreated(string $timestamp): self
    {
        $this->properties['CREATED'] = $timestamp;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.8 Security Properties
    // -------------------------------------------------------------------------

    /**
     * KEY – A public key or authentication certificate.
     * [RFC6350, Section 6.8.1]
     */
    public function addKey(string $key, string $mediaType = 'application/pgp-keys'): self
    {
        $this->properties['KEY'] = ['value' => $key, 'mediaType' => $mediaType];
        return $this;
    }

    // -------------------------------------------------------------------------
    // § 6.9 Calendar Properties
    // -------------------------------------------------------------------------

    /**
     * FBURL – A URI for the busy time associated with the object.
     * [RFC6350, Section 6.9.1]
     */
    public function addFburl(string $uri, string $type = ''): self
    {
        $this->append('FBURL', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /**
     * CALADRURI – A URI for a calendar user address.
     * [RFC6350, Section 6.9.2]
     */
    public function addCaladruri(string $uri, string $type = ''): self
    {
        $this->append('CALADRURI', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    /**
     * CALURI – A URI for a calendar associated with the object.
     * [RFC6350, Section 6.9.3]
     */
    public function addCaluri(string $uri, string $type = ''): self
    {
        $this->append('CALURI', $uri, $type !== '' ? ['type' => $type] : []);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § RFC6474 – Birth/Death Properties
    // -------------------------------------------------------------------------

    /**
     * BIRTHPLACE – The location of the object's birth.
     * [RFC6474, Section 2.1]
     */
    public function addBirthplace(string $place): self
    {
        $this->properties['BIRTHPLACE'] = $this->escape($place);
        return $this;
    }

    /**
     * DEATHPLACE – The location of the object's death.
     * [RFC6474, Section 2.2]
     */
    public function addDeathplace(string $place): self
    {
        $this->properties['DEATHPLACE'] = $this->escape($place);
        return $this;
    }

    /**
     * DEATHDATE – The date of death of the object the vCard represents.
     * [RFC6474, Section 2.3]
     */
    public function addDeathdate(string $date): self
    {
        $this->properties['DEATHDATE'] = $date;
        return $this;
    }

    // -------------------------------------------------------------------------
    // § RFC6715 – Expertise / Hobby / Interest Properties
    // -------------------------------------------------------------------------

    /**
     * EXPERTISE – A professional subject area(s) that the object has knowledge of.
     * level: beginner | average | expert
     * [RFC6715, Section 2.1]
     */
    public function addExpertise(string $area, string $level = 'average'): self
    {
        $this->append('EXPERTISE', $this->escape($area), ['level' => $level]);
        return $this;
    }

    /**
     * HOBBY – A recreational activity that the object actively engages in.
     * level: beginner | average | expert
     * [RFC6715, Section 2.2]
     */
    public function addHobby(string $hobby, string $level = 'average'): self
    {
        $this->append('HOBBY', $this->escape($hobby), ['level' => $level]);
        return $this;
    }

    /**
     * INTEREST – A recreational activity the object is interested in.
     * level: beginner | average | expert
     * [RFC6715, Section 2.3]
     */
    public function addInterest(string $interest, string $level = 'average'): self
    {
        $this->append('INTEREST', $this->escape($interest), ['level' => $level]);
        return $this;
    }

    // -------------------------------------------------------------------------
    // § RFC9555 – JSContact Properties
    // -------------------------------------------------------------------------

    /**
     * JSPROP – A JSON property for JSContact compatibility.
     * [RFC9555, Section 3.2.1]
     */
    public function addJsprop(string $key, string $jsonValue): self
    {
        $this->append('JSPROP', $jsonValue, ['jsptr' => $key]);
        return $this;
    }

    // -------------------------------------------------------------------------
    // Generic escape hatch for custom / vendor-prefixed properties
    // -------------------------------------------------------------------------

    /**
     * Add a custom or vendor-prefixed property (X- prefix is recommended).
     * e.g. addProperty('X-CUSTOM-FIELD', 'value')
     */
    public function addProperty(string $name, string $value, array $params = []): self
    {
        $this->append($name, $value, $params);
        return $this;
    }

    // -------------------------------------------------------------------------
    // Build
    // -------------------------------------------------------------------------

    public function build(): string
    {
        $lines = [
            'BEGIN:VCARD',
            'VERSION:' . $this->version,
        ];

        foreach ($this->properties as $key => $data) {
            if (is_array($data)) {
                // Multi-value list e.g. TEL, EMAIL, ADR, URL, SOCIALPROFILE …
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data as $item) {
                        $lines[] = $this->buildLine($key, $item);
                    }
                } else {
                    // Single structured value e.g. PHOTO, LOGO, SOUND, KEY, PRONOUNS
                    $lines[] = $this->buildLine($key, $data);
                }
            } else {
                $lines[] = $key . ':' . $data;
            }
        }

        $lines[] = 'REV:' . date('Y-m-d\TH:i:s\Z');
        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines) . "\r\n";
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Append a property item to the multi-value bucket for $key.
     */
    private function append(string $key, string $value, array $params = []): void
    {
        if (!isset($this->properties[$key])) {
            $this->properties[$key] = [];
        }
        $this->properties[$key][] = array_merge(['value' => $value], $params);
    }

    /**
     * Serialize a structured property item into a VCard line.
     */
    private function buildLine(string $key, array $data): string
    {
        $value = $data['value'] ?? '';
        $params = [];

        // TYPE parameter
        if (!empty($data['type'])) {
            $params[] = 'TYPE=' . strtoupper($data['type']);
        }

        // Media type / encoding (PHOTO, LOGO, SOUND, KEY)
        if (!empty($data['mediaType'])) {
            if (str_starts_with($value, 'http')) {
                $params[] = 'VALUE=URI';
            } else {
                $params[] = 'ENCODING=b';
                $params[] = 'TYPE=' . strtoupper($data['mediaType']);
            }
        }

        // LANGUAGE parameter
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
     * Escape special characters per RFC 6350 §3.4.
     * (commas and backslashes inside structured values must be escaped)
     */
    private function escape(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace("\r\n", "\n", $value);
        $value = str_replace("\n", '\n', $value);
        return $value;
    }
}
