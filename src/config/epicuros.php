<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Issuer Identifier
    |--------------------------------------------------------------------------
    |
    | This is the unique identifier of your api. This value will be used
    | by the server to locate your public key.
    |
    | Default: Your Laravel app name
    */

    'issuer' => config('app.name', 'Epicuros'),

    /*
    |--------------------------------------------------------------------------
    | JWT Signing Algrithm
    |--------------------------------------------------------------------------
    |
    | The algorithm to use when signing a JWT. Possible values are 'RS256'
    | for asychnonous (private/public) keys, and 'HS256', 'HS384', and
    | 'HS512' for shared key.
    |
    | Default: 'RS256'
    */

    'algorithm' => 'RS256',

    /*
    |--------------------------------------------------------------------------
    | Signing Key
    |--------------------------------------------------------------------------
    |
    | This is either the path to the service's private key from the
    | local storage folder in an asymetrical sign/verify configuration,
    | or the shared key in a shared key scenario.
    |
    | Default: none
    | Example: '/certs/epicuros-private.key',
    | Example: '6a628a71c4bc2c76048949a72ef9ac0d35d0dc5f3...s',
    */

    'signing_key' => '',


    /*
    |--------------------------------------------------------------------------
    | Token Expire
    |--------------------------------------------------------------------------
    |
    | The length in seconds that a signed JWT is valid for. It is
    | generally a best practice to keep the life of these tokens
    | to a minimum for security purposes.
    |
    | Default: 60,
    */

    'token_expire' => 60,

    /*
    |--------------------------------------------------------------------------
    | Public Key Mappings
    |--------------------------------------------------------------------------
    |
    | These are the mappings of clients services to their public key.
    | The array key is the client name, and the value is either a path
    | to the RSA key from the local storage folder or a shared HMAC.
    |
    | Default: none
    | Example: 'foo' => '/certs/client-public.key',
    | Example: 'bar' => '8016870d16216dafc58718698bf0...',
    */

    'key_mappings' => [
        //
    ],

];
