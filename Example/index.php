<?php
require_once "../vendor/autoload.php";
use MuhammetSafak\SecretCookies\Segment;

$cookie = new Segment('userInfo', [
    // OpenSSL encryption algorithm
    'algo'      => 'SHA256', // String

    // OpenSSL encryption method.
    'cipher'    => 'AES-256-CTR', // String

    // Top secret encryption key.
    'key'       => 'SecretCookie', // String

    // The lifetime of the cookie.
    'ttl'       => 3600, // Interger seconds

    // The cookie's path information.
    'path'      => '/', // String

    // The cookie's domain information. If it is Null or Null, it is not sent.
    'domain'    => null, // NULL or String

    // HTTPS only.
    'secure'    => false, // Boolean

    // HTTP only
    'httponly'  => true, // Boolean

    // SameSite
    'samesite'  => 'Strict', // String : "None", "Lax" or "Strict"
]);

echo $cookie->get('username', 'Undefined');

if(!$cookie->has('mail')){
    $cookie->set('username', 'muhammetsafak')
            ->set('mail', 'info@muhammetsafak.com.tr')
            ->save();
}
