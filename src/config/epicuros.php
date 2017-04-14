<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Service Identifier
    |--------------------------------------------------------------------------
    |
    | This is the unique identifier of your service. This value will be
    | used by the server to locate the appropriate verification key and
    | to determine if it is the intended audience for the request.
    |
    | Default: 'Epicuros',
    */

    'service' => config('app.name', 'Epicuros'),

    /*
    |--------------------------------------------------------------------------
    | Token Signing Algrithm
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
    | Example: '/certs/foo-private.key',
    | Example: '6a628a71c4bc2c76048949a72ef9ac0d35d0dc5f3...',
    */

    'signing_key' => '',


    /*
    |--------------------------------------------------------------------------
    | Key Mappings
    |--------------------------------------------------------------------------
    |
    | These are the mappings of services to their respective RSA public
    | keys or shared secrets. The array key is the name of the token issuer,
    | and the value is either the path to its public key from the local
    | storage folder or the shared secret.
    |
    | Default: none
    | Example: 'foo' => '/certs/foo-public.key',
    | Example: 'bar' => '6a628a71c4bc2c76048949a72ef9ac0d35d0dc5f3...',
    */

    'key_mappings' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Expiry
    |--------------------------------------------------------------------------
    |
    | The time in seconds that a signed token is valid for. It is
    | generally a best practice to keep the life of these tokens
    | to a minimum for security purposes.
    |
    | Default: 30,
    */

    'token_expire' => 30,

];
