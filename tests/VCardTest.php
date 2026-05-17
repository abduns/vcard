<?php

use Dunn\VCard\VCard;

it('generates a simple vcard', function () {
    $vcard = VCard::make()
        ->addName('Doe', 'John')
        ->addEmail('john@example.com')
        ->addPhoneNumber('123456789')
        ->build();

    expect($vcard)->toContain('BEGIN:VCARD');
    expect($vcard)->toContain('VERSION:3.0');
    expect($vcard)->toContain('N:Doe;John;;;');
    expect($vcard)->toContain('FN:John Doe');
    expect($vcard)->toContain('EMAIL;TYPE=INTERNET:john@example.com');
    expect($vcard)->toContain('TEL;TYPE=CELL:123456789');
    expect($vcard)->toContain('END:VCARD');
});

it('escapes characters correctly', function () {
    $vcard = VCard::make()
        ->addNote("Line 1\nLine 2, and some comma")
        ->build();

    expect($vcard)->toContain('NOTE:Line 1\nLine 2\, and some comma');
});
