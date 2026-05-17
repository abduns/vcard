<?php

use Dunn\VCard\VCard;

// -------------------------------------------------------------------------
// Core Build
// -------------------------------------------------------------------------

it('builds a valid vcard with begin and end markers', function () {
    $output = VCard::make()->addFormattedName('John Doe')->build();
    expect($output)->toStartWith("BEGIN:VCARD\r\n");
    expect($output)->toContain("END:VCARD\r\n");
    expect($output)->toContain('VERSION:3.0');
});

it('throws if FN is missing when calling build()', function () {
    VCard::make()->addPhoneNumber('123456789')->build();
})->throws(\InvalidArgumentException::class, 'A formatted name (FN) is required');

it('defaults to version 3.0', function () {
    expect(VCard::make()->addFormattedName('Test')->build())->toContain('VERSION:3.0');
});

it('supports version 4.0', function () {
    expect(VCard::make('4.0')->addFormattedName('Test')->build())->toContain('VERSION:4.0');
});

// -------------------------------------------------------------------------
// § 6.1 General Properties
// -------------------------------------------------------------------------

it('supports SOURCE', function () {
    $out = VCard::make()->addFormattedName('Test')->addSource('https://example.com/vcard')->build();
    expect($out)->toContain('SOURCE:https://example.com/vcard');
});

it('supports KIND', function () {
    $out = VCard::make()->addFormattedName('Test')->addKind('individual')->build();
    expect($out)->toContain('KIND:individual');
});

it('supports XML', function () {
    $out = VCard::make()->addFormattedName('Test')->addXml('<x:foo/>')->build();
    expect($out)->toContain("XML:<x:foo/>");
});

// -------------------------------------------------------------------------
// § 6.2 Identification Properties
// -------------------------------------------------------------------------

it('supports N and auto-generates FN', function () {
    $out = VCard::make()->addName('Doe', 'John', '', 'Mr.', 'Jr.')->build();
    expect($out)->toContain('N:Doe;John;;Mr.;Jr.');
    expect($out)->toContain('FN:Mr. John Doe Jr.');
});

it('supports FN and overrides auto-generated one', function () {
    $out = VCard::make()
        ->addName('Doe', 'John')
        ->addFormattedName('J. Doe')
        ->build();
    expect($out)->toContain('FN:J. Doe');
});

it('supports NICKNAME (multiple)', function () {
    $out = VCard::make()->addFormattedName('Test')->addNickname('JD', 'Johnny')->build();
    expect($out)->toContain('NICKNAME:JD,Johnny');
});

it('supports PHOTO via URL', function () {
    $out = VCard::make()->addFormattedName('Test')->addPhoto('https://example.com/photo.jpg')->build();
    expect($out)->toContain('PHOTO;VALUE=URI:https://example.com/photo.jpg');
});

it('supports BDAY', function () {
    $out = VCard::make()->addFormattedName('Test')->addBirthday('1990-01-15')->build();
    expect($out)->toContain('BDAY:1990-01-15');
});

it('supports ANNIVERSARY', function () {
    $out = VCard::make()->addFormattedName('Test')->addAnniversary('2015-06-20')->build();
    expect($out)->toContain('ANNIVERSARY:2015-06-20');
});

it('supports GENDER sex only', function () {
    $out = VCard::make()->addFormattedName('Test')->addGender('M')->build();
    expect($out)->toContain('GENDER:M');
});

it('supports GENDER with identity', function () {
    $out = VCard::make()->addFormattedName('Test')->addGender('O', 'non-binary')->build();
    expect($out)->toContain('GENDER:O;non-binary');
});

it('supports GRAMGENDER', function () {
    $out = VCard::make()->addFormattedName('Test')->addGramGender('Masculine')->build();
    expect($out)->toContain('GRAMGENDER:masculine');
});

it('supports PRONOUNS', function () {
    $out = VCard::make()->addFormattedName('Test')->addPronouns('they/them', 'en')->build();
    expect($out)->toContain('PRONOUNS;LANGUAGE=en:they/them');
});

it('supports LANGUAGE', function () {
    $out = VCard::make()->addFormattedName('Test')->addLanguage('en-US')->build();
    expect($out)->toContain('LANGUAGE:en-US');
});

// -------------------------------------------------------------------------
// § 6.3 Delivery Addressing
// -------------------------------------------------------------------------

it('supports ADR with type', function () {
    $out = VCard::make()->addFormattedName('Test')
        ->addAddress('', 'Suite 1', '123 Main St', 'New York', 'NY', '10001', 'USA', 'WORK')
        ->build();
    expect($out)->toContain('ADR;TYPE=WORK:;Suite 1;123 Main St;New York;NY;10001;USA');
});

it('supports multiple ADR entries', function () {
    $out = VCard::make()->addFormattedName('Test')
        ->addAddress('', '', '1 Home St', 'Town', 'ST', '00000', 'US', 'HOME')
        ->addAddress('', '', '2 Work Ave', 'City', 'ST', '11111', 'US', 'WORK')
        ->build();
    expect($out)->toContain('ADR;TYPE=HOME');
    expect($out)->toContain('ADR;TYPE=WORK');
});

// -------------------------------------------------------------------------
// § 6.4 Communications Properties
// -------------------------------------------------------------------------

it('supports TEL with type', function () {
    $out = VCard::make()->addFormattedName('Test')->addPhoneNumber('+1234567890', 'CELL')->build();
    expect($out)->toContain('TEL;TYPE=CELL:+1234567890');
});

it('supports multiple TEL entries', function () {
    $out = VCard::make()->addFormattedName('Test')
        ->addPhoneNumber('111', 'CELL')
        ->addPhoneNumber('222', 'WORK')
        ->build();
    expect($out)->toContain('TEL;TYPE=CELL:111');
    expect($out)->toContain('TEL;TYPE=WORK:222');
});

it('supports EMAIL with type', function () {
    $out = VCard::make()->addFormattedName('Test')->addEmail('john@example.com', 'WORK')->build();
    expect($out)->toContain('EMAIL;TYPE=WORK:john@example.com');
});

it('supports IMPP', function () {
    $out = VCard::make()->addFormattedName('Test')->addImpp('xmpp:user@jabber.org')->build();
    expect($out)->toContain('IMPP:xmpp:user@jabber.org');
});

it('supports IMPP with type', function () {
    $out = VCard::make()->addFormattedName('Test')->addImpp('sip:user@example.com', 'WORK')->build();
    expect($out)->toContain('IMPP;TYPE=WORK:sip:user@example.com');
});

it('supports LANG', function () {
    $out = VCard::make()->addFormattedName('Test')->addLang('fr', 'WORK')->build();
    expect($out)->toContain('LANG;TYPE=WORK:fr');
});

it('supports SOCIALPROFILE with service type', function () {
    $out = VCard::make()->addFormattedName('Test')
        ->addSocialProfile('https://github.com/johndoe', 'GitHub')
        ->build();
    expect($out)->toContain('SOCIALPROFILE;SERVICE-TYPE=GitHub:https://github.com/johndoe');
});

it('supports CONTACT-URI', function () {
    $out = VCard::make()->addFormattedName('Test')->addContactUri('https://example.com/contact')->build();
    expect($out)->toContain('CONTACT-URI:https://example.com/contact');
});

// -------------------------------------------------------------------------
// § 6.5 Geographical Properties
// -------------------------------------------------------------------------

it('supports TZ', function () {
    $out = VCard::make()->addFormattedName('Test')->addTz('America/New_York')->build();
    expect($out)->toContain('TZ:America/New_York');
});

it('supports GEO', function () {
    $out = VCard::make()->addFormattedName('Test')->addGeo('geo:37.386013,-122.082932')->build();
    expect($out)->toContain('GEO:geo:37.386013,-122.082932');
});

// -------------------------------------------------------------------------
// § 6.6 Organizational Properties
// -------------------------------------------------------------------------

it('supports TITLE', function () {
    $out = VCard::make()->addFormattedName('Test')->addJobTitle('Software Engineer')->build();
    expect($out)->toContain('TITLE:Software Engineer');
});

it('supports ROLE', function () {
    $out = VCard::make()->addFormattedName('Test')->addRole('Backend Lead')->build();
    expect($out)->toContain('ROLE:Backend Lead');
});

it('supports LOGO via URL', function () {
    $out = VCard::make()->addFormattedName('Test')->addLogo('https://example.com/logo.png')->build();
    expect($out)->toContain('LOGO;VALUE=URI:https://example.com/logo.png');
});

it('supports ORG with units', function () {
    $out = VCard::make()->addFormattedName('Test')->addCompany('Acme Corp', 'Engineering', 'Backend')->build();
    expect($out)->toContain('ORG:Acme Corp;Engineering;Backend');
});

it('supports MEMBER', function () {
    $out = VCard::make()->addFormattedName('Test')->addMember('mailto:user@example.com')->build();
    expect($out)->toContain('MEMBER:mailto:user@example.com');
});

it('supports RELATED', function () {
    $out = VCard::make()->addFormattedName('Test')->addRelated('urn:uuid:abc123', 'friend')->build();
    expect($out)->toContain('RELATED;TYPE=FRIEND:urn:uuid:abc123');
});

it('supports ORG-DIRECTORY', function () {
    $out = VCard::make()->addFormattedName('Test')->addOrgDirectory('ldap://example.com/')->build();
    expect($out)->toContain('ORG-DIRECTORY:ldap://example.com/');
});

// -------------------------------------------------------------------------
// § 6.7 Explanatory Properties
// -------------------------------------------------------------------------

it('supports CATEGORIES', function () {
    $out = VCard::make()->addFormattedName('Test')->addCategories('Colleague', 'Friend', 'Developer')->build();
    expect($out)->toContain('CATEGORIES:Colleague,Friend,Developer');
});

it('supports NOTE', function () {
    $out = VCard::make()->addFormattedName('Test')->addNote('A great contact.')->build();
    expect($out)->toContain('NOTE:A great contact.');
});

it('supports PRODID', function () {
    $out = VCard::make()->addFormattedName('Test')->addProdid('-//MyApp//EN')->build();
    expect($out)->toContain('PRODID:-//MyApp//EN');
});

it('supports SOUND via URL', function () {
    $out = VCard::make()->addFormattedName('Test')->addSound('https://example.com/sound.ogg')->build();
    expect($out)->toContain('SOUND;VALUE=URI:https://example.com/sound.ogg');
});

it('supports UID', function () {
    $out = VCard::make()->addFormattedName('Test')->addUid('urn:uuid:12345678-1234-5678-1234-567812345678')->build();
    expect($out)->toContain('UID:urn:uuid:12345678-1234-5678-1234-567812345678');
});

it('supports CLIENTPIDMAP', function () {
    $out = VCard::make()->addFormattedName('Test')->addClientpidmap(1, 'urn:uuid:abc-def')->build();
    expect($out)->toContain('CLIENTPIDMAP:1;urn:uuid:abc-def');
});

it('supports URL', function () {
    $out = VCard::make()->addFormattedName('Test')->addUrl('https://example.com', 'WORK')->build();
    expect($out)->toContain('URL;TYPE=WORK:https://example.com');
});

it('supports CREATED', function () {
    $out = VCard::make()->addFormattedName('Test')->addCreated('2024-01-01T00:00:00Z')->build();
    expect($out)->toContain('CREATED:2024-01-01T00:00:00Z');
});

// -------------------------------------------------------------------------
// § 6.8 Security Properties
// -------------------------------------------------------------------------

it('supports KEY via URL', function () {
    $out = VCard::make()->addFormattedName('Test')->addKey('https://example.com/key.asc', 'application/pgp-keys')->build();
    expect($out)->toContain('KEY;VALUE=URI:https://example.com/key.asc');
});

// -------------------------------------------------------------------------
// § 6.9 Calendar Properties
// -------------------------------------------------------------------------

it('supports FBURL', function () {
    $out = VCard::make()->addFormattedName('Test')->addFburl('https://cal.example.com/busy/jdoe', 'WORK')->build();
    expect($out)->toContain('FBURL;TYPE=WORK:https://cal.example.com/busy/jdoe');
});

it('supports CALADRURI', function () {
    $out = VCard::make()->addFormattedName('Test')->addCaladruri('mailto:jdoe@example.com', 'WORK')->build();
    expect($out)->toContain('CALADRURI;TYPE=WORK:mailto:jdoe@example.com');
});

it('supports CALURI', function () {
    $out = VCard::make()->addFormattedName('Test')->addCaluri('https://cal.example.com/jdoe', 'WORK')->build();
    expect($out)->toContain('CALURI;TYPE=WORK:https://cal.example.com/jdoe');
});

// -------------------------------------------------------------------------
// § RFC6474 – Birth/Death
// -------------------------------------------------------------------------

it('supports BIRTHPLACE', function () {
    $out = VCard::make()->addFormattedName('Test')->addBirthplace('Paris, France')->build();
    expect($out)->toContain('BIRTHPLACE:Paris');
});

it('supports DEATHPLACE', function () {
    $out = VCard::make()->addFormattedName('Test')->addDeathplace('London, UK')->build();
    expect($out)->toContain('DEATHPLACE:London');
});

it('supports DEATHDATE', function () {
    $out = VCard::make()->addFormattedName('Test')->addDeathdate('2050-12-31')->build();
    expect($out)->toContain('DEATHDATE:2050-12-31');
});

// -------------------------------------------------------------------------
// § RFC6715 – Expertise / Hobby / Interest
// -------------------------------------------------------------------------

it('supports EXPERTISE with level', function () {
    $out = VCard::make()->addFormattedName('Test')->addExpertise('PHP', 'expert')->build();
    expect($out)->toContain('EXPERTISE;LEVEL=expert:PHP');
});

it('supports HOBBY with level', function () {
    $out = VCard::make()->addFormattedName('Test')->addHobby('Rock Climbing', 'average')->build();
    expect($out)->toContain('HOBBY;LEVEL=average:Rock Climbing');
});

it('supports INTEREST with level', function () {
    $out = VCard::make()->addFormattedName('Test')->addInterest('Open Source', 'expert')->build();
    expect($out)->toContain('INTEREST;LEVEL=expert:Open Source');
});

// -------------------------------------------------------------------------
// § Generic / Custom
// -------------------------------------------------------------------------

it('supports custom X- properties', function () {
    $out = VCard::make()->addFormattedName('Test')->addProperty('X-CUSTOM', 'myvalue')->build();
    expect($out)->toContain('X-CUSTOM:myvalue');
});

// -------------------------------------------------------------------------
// § Escape handling
// -------------------------------------------------------------------------

it('escapes backslashes', function () {
    $out = VCard::make()->addFormattedName('Test')->addNote('path\\to\\file')->build();
    expect($out)->toContain('NOTE:path\\\\to\\\\file');
});

it('escapes newlines in note', function () {
    $out = VCard::make()->addFormattedName('Test')->addNote("Line 1\nLine 2")->build();
    expect($out)->toContain("NOTE:Line 1\\nLine 2");
});
