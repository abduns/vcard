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

    public function addName(string $lastName, string $firstName, string $additional = '', string $prefix = '', string $suffix = ''): self
    {
        $this->properties['N'] = implode(';', [$lastName, $firstName, $additional, $prefix, $suffix]);
        
        $formattedName = trim(implode(' ', array_filter([$prefix, $firstName, $additional, $lastName, $suffix])));
        if (!isset($this->properties['FN'])) {
            $this->properties['FN'] = $formattedName;
        }

        return $this;
    }

    public function addFormattedName(string $formattedName): self
    {
        $this->properties['FN'] = $formattedName;
        return $this;
    }

    public function addCompany(string $company): self
    {
        $this->properties['ORG'] = $company;
        return $this;
    }

    public function addJobTitle(string $jobTitle): self
    {
        $this->properties['TITLE'] = $jobTitle;
        return $this;
    }

    public function addEmail(string $email, string $type = 'INTERNET'): self
    {
        if (!isset($this->properties['EMAIL'])) {
            $this->properties['EMAIL'] = [];
        }
        $this->properties['EMAIL'][] = ['value' => $email, 'type' => $type];
        return $this;
    }

    public function addPhoneNumber(string $number, string $type = 'CELL'): self
    {
        if (!isset($this->properties['TEL'])) {
            $this->properties['TEL'] = [];
        }
        $this->properties['TEL'][] = ['value' => $number, 'type' => $type];
        return $this;
    }

    public function addAddress(string $name, string $extended, string $street, string $city, string $region, string $zip, string $country, string $type = 'WORK'): self
    {
        if (!isset($this->properties['ADR'])) {
            $this->properties['ADR'] = [];
        }
        
        // ADR format: post office box;extended address;street address;locality;region;postal code;country name
        $value = implode(';', [$name, $extended, $street, $city, $region, $zip, $country]);
        $this->properties['ADR'][] = ['value' => $value, 'type' => $type];
        return $this;
    }

    public function addUrl(string $url, string $type = 'WORK'): self
    {
        if (!isset($this->properties['URL'])) {
            $this->properties['URL'] = [];
        }
        $this->properties['URL'][] = ['value' => $url, 'type' => $type];
        return $this;
    }

    public function addNote(string $note): self
    {
        $this->properties['NOTE'] = $note;
        return $this;
    }

    public function addPhoto(string $urlOrBase64, string $type = 'JPEG'): self
    {
        $this->properties['PHOTO'] = ['value' => $urlOrBase64, 'type' => $type];
        return $this;
    }

    public function build(): string
    {
        $lines = [
            'BEGIN:VCARD',
            'VERSION:' . $this->version,
        ];

        foreach ($this->properties as $key => $data) {
            if (is_array($data)) {
                if (isset($data['value']) && isset($data['type'])) { // single complex like PHOTO
                    $this->buildComplexProperty($lines, $key, $data);
                } else { // array of complex like TEL, EMAIL, ADR, URL
                    foreach ($data as $item) {
                        $this->buildComplexProperty($lines, $key, $item);
                    }
                }
            } else {
                $lines[] = $key . ':' . $this->escape($data);
            }
        }

        $lines[] = 'REV:' . date('Y-m-d\TH:i:s\Z');
        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines) . "\r\n";
    }

    private function buildComplexProperty(array &$lines, string $key, array $data): void
    {
        $value = $this->escape($data['value']);
        $type = $data['type'];

        if ($key === 'PHOTO') {
            if (str_starts_with($value, 'http')) {
                $lines[] = "PHOTO;VALUE=URI;TYPE={$type}:{$value}";
            } else {
                $lines[] = "PHOTO;ENCODING=b;TYPE={$type}:{$value}";
            }
        } elseif ($key === 'ADR') {
            $lines[] = "{$key};TYPE={$type}:{$value}"; // value is already formatted
        } else {
            $lines[] = "{$key};TYPE={$type}:{$value}";
        }
    }

    private function escape(string $value): string
    {
        $value = str_replace("\r\n", "\n", $value);
        $value = str_replace("\n", '\n', $value);
        $value = str_replace(',', '\,', $value);
        return $value;
    }
}
