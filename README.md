# Secret Cookies

It offers a more secure cookie storage opportunity by encrypting cookies.

[![Latest Stable Version](http://poser.pugx.org/muhammetsafak/secret-cookie/v)](https://packagist.org/packages/muhammetsafak/secret-cookie) [![Total Downloads](http://poser.pugx.org/muhammetsafak/secret-cookie/downloads)](https://packagist.org/packages/muhammetsafak/secret-cookie) [![Latest Unstable Version](http://poser.pugx.org/muhammetsafak/secret-cookie/v/unstable)](https://packagist.org/packages/muhammetsafak/secret-cookie) [![License](http://poser.pugx.org/muhammetsafak/secret-cookie/license)](https://packagist.org/packages/muhammetsafak/secret-cookie) [![PHP Version Require](http://poser.pugx.org/muhammetsafak/secret-cookie/require/php)](https://packagist.org/packages/muhammetsafak/secret-cookie)

## Requirements

- PHP 7.4 or higher
- [InitPHP ParameterBag Library](https://github.com/InitPHP/ParameterBag)
- [InitPHP Encryption Library](https://github.com/InitPHP/Encryption)

_**Note :**_ The above libraries may have specific requirements (like **OpenSSL** and **MB_String**).

## Installation

```
composer require muhammetsafak/secret-cookies
```

## Configuration

```php
$options = [
        'algo'      => 'SHA256', // String : OpenSSL Algorithm
        'cipher'    => 'AES-256-CTR', // String : OpenSSL Cipher
        'key'       => 'SecretCookie', // String : Top Secret Key
        'ttl'       => 3600, // Integer : Seconds - LifeTime
        'path'      => '/', // String
        'domain'    => null, // Null or String. If it is empty, it is not used.
        'secure'    => false, // Boolean
        'httponly'  => true, // Boolean
        'samesite'  => 'Strict', // "None", "Lax" or "Strict"
];
```

_**Very Important Note :**_ For security purposes, the `key` must be specified. Otherwise, using this library is just a burden for your server. Users' cookie data is encrypted and decrypted with this key.

## Usage

```php
require_once "vendor/autoload.php";
use MuhammetSafak\SecretCookies\Segment;

// See the configuration section for detailed information.
$options = [];

$cookie = new Segment('cookieName', $options);

$cookie->set('username', 'muhammetsafak')
        ->set('mail', 'info@muhammetsafak.com.tr');
```

### Performance

Encryption and decryption can become a huge burden for servers in some cases. This library; it tries to avoid a repeated encryption and decryption every time.

Normally, decryption is performed with the `__construct()` method only, and encryption with the `__destruct()` method. If you still manage to escape the `__destruct()` method for some reason; you have the `save()` method that will make the changes permanent by sending them to the user's browser.


### Methods

#### `has()`

It checks if the data is defined using the current key in the segment.

```php
public function has(string $key): bool;
```

#### `get()`

Returns the value of the specified key. Otherwise `$default` returns the given value.

```php
public function get(string $key, $default = null): mixed;
```

#### `set()`

Defines the value of the specified key.

```php
public function set(string $key, $value): self;
```

_Note :_ This method change takes effect after it but does not send it directly to the user's browser. The `save()` method should work or the object should terminate correctly for the changes to be sent to the user browser. Why and in which case the `save()` method is a must is explained in the [Performance](#performance) section.

#### `remove()`

```php
public function remove(string $key): self;
```

_Note :_ This method change takes effect after it but does not send it directly to the user's browser. The `save()` method should work or the object should terminate correctly for the changes to be sent to the user browser. Why and in which case the `save()` method is a must is explained in the [Performance](#performance) section.

#### `save()`

If any, it sends the changes to the user's browser, making them permanent/valid. If the object is terminated correctly; PHP will run it automatically with the help of the `__destruct()` method.

```php
public function save(): void;
```

#### `getDebug()`

If a known error is encountered; we keep it in an array. The `getDebug()` method returns known errors, if any.

```php
public function getDebug(): string[];
```

## Getting Help

If you have questions, concerns, bug reports, etc, please file an issue in this repository's Issue Tracker.

## Contributing

> All contributions to this project will be published under the MIT License. By submitting a pull request or filing a bug, issue, or feature request, you are agreeing to comply with this waiver of copyright interest.

- Fork it ( [https://github.com/muhammetsafak/secret-cookies/fork](https://github.com/muhammetsafak/secret-cookies/fork) )
- Create your feature branch (git checkout -b my-new-feature)
- Commit your changes (git commit -am "Add some feature")
- Push to the branch (git push origin my-new-feature)
- Create a new Pull Request

## Credits

- [Muhammet ŞAFAK](https://www.muhammetsafak.com.tr) <<info@muhammetsafak.com.tr>>

## License

Copyright &copy; 2022 [MIT License](./LICENSE)
