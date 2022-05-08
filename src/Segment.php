<?php
/**
 * Translator.php
 *
 * This file is part of Secret Cookies.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 Muhammet ŞAFAK
 * @license    https://github.com/muhammetsafak/secret-cookies/blob/main/LICENSE  MIT
 * @version    1.0
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);
namespace MuhammetSafak\SecretCookies;

use \InitPHP\Encryption\{HandlerInterface, Encrypt, OpenSSL};
use \InitPHP\ParameterBag\ParameterBag;

use function array_merge;
use function time;
use function is_int;
use function is_bool;
use function in_array;
use function strtolower;
use function setcookie;

/**
 *
 */
class Segment
{

    protected string $segment;

    protected array $configs = [
        'algo'      => 'SHA256',
        'cipher'    => 'AES-256-CTR',
        'key'       => 'SecretCookie',
        'ttl'       => 3600,
        'path'      => '/',
        'domain'    => null,
        'secure'    => false,
        'httponly'  => true,
        'samesite'  => 'Strict', // [None|Lax|Strict]
    ];

    protected ParameterBag $cookies;

    protected HandlerInterface $encrypt;

    protected array $errors = [];

    protected bool $isChanged = false;

    public function __construct(string $segment, array $configs = [])
    {
        if(!empty($configs)){
            $this->configs = array_merge($this->configs, $configs);
        }
        $this->segment = $segment;
        $this->encrypt = Encrypt::use(OpenSSL::class, [
            'algo'      => $this->configs['algo'],
            'cipher'    => $this->configs['cipher'],
            'key'       => $this->configs['key'],
        ]);
        $this->resolve();
    }

    public function __destruct()
    {
        $this->save();
    }

    /**
     * It checks if the data is defined using the current key in the segment.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->cookies->has($key);
    }

    /**
     * Returns the value of the specified key. Otherwise $default returns the given value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->cookies->get($key, $default);
    }

    /**
     * Defines the value of the specified key.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $this->isChanged = true;
        $this->cookies->set($key, $value);
        return $this;
    }

    /**
     * Removes the specified key from segment cookies.
     *
     * @param string $key
     * @return $this
     */
    public function remove(string $key): self
    {
        $this->isChanged = true;
        $this->cookies->remove($key);
        return $this;
    }

    /**
     * It encrypts the created or changed cookie values and transmits them to the user's browser.
     *
     * @used-by Segment::__destruct()
     * @return void
     */
    public function save(): void
    {
        if($this->isChanged !== FALSE){
            $this->handle();
        }
        $this->isChanged = false;
    }

    /**
     * Returns an array of issues and notifications, if any.
     *
     * @return string[]
     */
    public function getDebug(): array
    {
        return $this->errors;
    }

    /**
     * It parses the segment's cookies and loads them into the class.
     *
     * @used-by Segment::__construct()
     * @return void
     */
    protected function resolve()
    {
        $cookie = $_COOKIE[$this->segment] ?? null;
        if(empty($cookie)){
            $this->errors[] = "The " . $this->segment . " segment is not found in the user's browser.";
            $this->cookies = new ParameterBag([]);
            return;
        }
        $cookies = $this->encrypt->decrypt($cookie);
        if(empty($cookies)){
            $this->errors[] = "Segment " . $this->segment . " could not be decrypted; an empty or invalid value.";
            $this->cookies = new ParameterBag([]);
            return;
        }
        $this->cookies = new ParameterBag($cookies);
    }

    /**
     * It encrypts the segment's cookies and sends them to the user's browser.
     *
     * @used-by Segment::save()
     * @return void
     */
    protected function handle()
    {
        $cookies = isset($this->cookies) ? $this->cookies->all() : [];
        $cookie = $this->encrypt->encrypt($cookies);
        if(!is_int($this->configs['ttl'])){
            $this->configs['ttl'] = 3600;
            $this->errors[] = 'The default value is used because the "ttl" value is invalid.';
        }
        if(!is_bool($this->configs['secure'])){
            $this->configs['secure'] = false;
            $this->errors[] = 'The default value is used because the "secure" value is invalid.';
        }
        if(!is_bool($this->configs['httponly'])){
            $this->configs['httponly'] = true;
            $this->errors[] = 'The default value is used because the "httponly" value is invalid.';
        }
        if(!in_array(strtolower($this->configs['samesite']), ['none', 'lax', 'strict'], true)){
            $this->configs['samesite'] = 'Strict';
            $this->errors[] = 'The default value is used because the "samesite" value is invalid.';
        }
        $options = [
            'expires'   => (time() + $this->configs['ttl']),
            'path'      => $this->configs['path'],
            'secure'    => $this->configs['secure'],
            'httponly'  => $this->configs['httponly'],
            'samesite'  => $this->configs['samesite'],
        ];
        if(!empty($this->configs['domain'])){
            $options['domain'] = $this->configs['domain'];
        }
        setcookie($this->segment, $cookie, $options);
    }

}
