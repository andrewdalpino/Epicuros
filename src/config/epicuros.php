<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Issuer Identifier
    |--------------------------------------------------------------------------
    |
    | This is the unique identifier of your api. This value will be used
    | by the server to locate your RSA public key.
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
    | for asychnonous (private/public) keys, or 'HS256', 'HS384', and
    | 'HS512' for shared secret.
    |
    | Default: 'HS512'
    */

    'algorithm' => 'HS512',

    /*
    |--------------------------------------------------------------------------
    | Signing Key
    |--------------------------------------------------------------------------
    |
    | This is either the path to the service's private key from the
    | local storage folder in an asymetrical sign/verify configuration,
    | or the shared key in a shared secret scenario.
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
    | Default: 30,
    */

    'token_expire' => 30,

    /*
    |--------------------------------------------------------------------------
    | RSA Public Key Mappings
    |--------------------------------------------------------------------------
    |
    | These are the mappings of services to their respective RSA public
    | keys. The array key is the name of the token issuer, and the value
    | is the path to its public key from the local storage folder.
    |
    | Default: none
    | Example: 'foo' => '/certs/foo-public.key',
    */

    'public_key_mappings' => [
        //
    ],

];
