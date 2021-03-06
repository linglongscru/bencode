# PHP Bencode Encoder/Decoder

[![Packagist](https://img.shields.io/packagist/v/sandfoxme/bencode.svg?maxAge=2592000)](https://packagist.org/packages/sandfoxme/bencode)
[![Packagist](https://img.shields.io/packagist/l/sandfoxme/bencode.svg?maxAge=2592000)](https://opensource.org/licenses/MIT)
[![Travis](https://img.shields.io/travis/sandfoxme/bencode.svg?maxAge=2592000)](https://travis-ci.org/sandfoxme/bencode)
[![Code Climate](https://img.shields.io/codeclimate/coverage/github/sandfoxme/bencode.svg?maxAge=2592000)](https://codeclimate.com/github/sandfoxme/bencode/coverage)
[![Code Climate](https://img.shields.io/codeclimate/maintainability/sandfoxme/bencode.svg?maxAge=2592000)](https://codeclimate.com/github/sandfoxme/bencode)

[Bencode](https://en.wikipedia.org/wiki/Bencode) is the encoding used by the peer-to-peer file sharing system
[BitTorrent](https://en.wikipedia.org/wiki/BitTorrent) for storing and transmitting loosely structured data.

This is a pure PHP library that allows you to encode and decode Bencode data.

## Encoding

```php
<?php

use SandFoxMe\Bencode\Bencode;

// scalars and arrays

$encoded = Bencode::encode([    // array will become dictionary
    'arr'       => [1,2,3,4],       // sequential array will become a list
    'int'       => 123,             // integer is stored as is
    'float'     => 3.1415,          // float will become a string
    'bool'      => true,            // bool will be an integer 1 or 0
    'string'    => "test\0test",    // string can contain any binary data
]); // "d3:arrli1ei2ei3ei4ee4:booli1e5:float6:3.14153:inti123e6:string9:test\0teste"

// objects

// traversable objects and stdClass become dictionaries
Bencode::encode(new ArrayObject([1,2,3])); // "d1:0i1e1:1i2e1:2i3ee"
$std = new stdClass(); 
$std->a = '123'; 
$std->b = 456;
Bencode::encode($std); // "d1:a3:1231:bi456ee"

// you can force traversable to become a list by wrapping it with SandFoxMe\Bencode\Types\ListType
// keys will be discarded in that case
use SandFoxMe\Bencode\Types\ListType;
Bencode::encode(new ListType(new ArrayObject([1,2,3]))); // "li1ei2ei3ee"

// other objects will be converted to string if possible or generate an error if not
Bencode::encode(new class { function __toString() { return 'I am string'; } }); // "11:I am string"
```

## Decoding

```php
<?php

use SandFoxMe\Bencode\Bencode;

// simple decoding, lists and dictionaries will be arrays
Bencode::decode("d3:arrli1ei2ei3ei4ee4:booli1e5:float6:3.14153:inti123e6:string9:test\0teste");
// [
//   "arr" => [1,2,3,4],
//   "bool" => 1,
//   "float" => "3.1415",
//   "int" => 123,
//   "string" => "test\0test",
// ]

// You can control lists and dictionaries types with options
Bencode::decode("...", [
    'dictionaryType'    => ArrayObject::class, // pass class name, new $type($array) will be created
    'listType'          => function ($array) { // or callback for greater flexibility
        return new ArrayObject($array, ArrayObject::ARRAY_AS_PROPS);
    },
]);
// default value for both types is 'array'. you can also use 'object' for stdClass
```

## Working with files

```php
<?php

use SandFoxMe\Bencode\Bencode;

$data = Bencode::load('testfile.torrent'); // load data from bencoded file
Bencode::dump('testfile.torrent', $data); // save data to the bencoded file
```

## Installation

Add this to your `composer.json`:

```json
{
    "require": {
        "sandfoxme/bencode": "^1.0.0"
    }
}
```

or run `composer require 'sandfoxme/bencode:^1.0.0'`.

## License

The library is available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).
