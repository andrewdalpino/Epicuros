<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client Name
    |--------------------------------------------------------------------------
    |
    | This is the unique name of your service for the purposes of
    | identifying itself to the server. In a private/public key
    | configuration, this value will be used to locate the public
    | key on the server side.
    |
    | Default: Your Laravel app name
    */

    'client_name' => config('app.name', 'Epicuros'),

    /*
    |--------------------------------------------------------------------------
    | JWT Signing Algrithm
    |--------------------------------------------------------------------------
    |
    | The algorithm to use when signing a JWT. Possible values are 'RS256'
    | for OpenSSL asychnonous (private/public) keys, and 'HS256', 'HS384',
    | and 'HS512' for HMAC shared key.
    |
    | Default: 'RS256'
    */

    'algorithm' => 'RS256',

    /*
    |--------------------------------------------------------------------------
    | Signing Key
    |--------------------------------------------------------------------------
    |
    | This is either the location of the private key in the
    | storage path when using OpenSSL asymetrical cryptography,
    | or the shared key when using HMAC.
    |
    | Default: '/certs/epicuros-private.key',
    */

    'signing_key' => '/certs/epicuros-private.key',


    /*
    |--------------------------------------------------------------------------
    | JWT Expire
    |--------------------------------------------------------------------------
    |
    | The length in seconds that a signed JWT is valid for. It is
    | generally a best practice to keep the life of these tokens
    | to a minimum for security purposes.
    |
    | Default: 60,
    */

    'jwt_expire' => 60,

    /*
    |--------------------------------------------------------------------------
    | Request Headers
    |--------------------------------------------------------------------------
    |
    | You may specify any headers you would like to send with each request.
    | It is generally best to specify at least the 'Content-Type', and
    | 'Accept' headers by default.
    |
    | Default:
    |
    | 'User-Agent' => config('app.name', 'Epicuros'),
    | 'Content-Type' => 'application/json',
    | 'Accept' => 'application/json',
    |
    */

   'headers' => [
       'User-Agent' => config('app.name', 'Epicuros'),
       'Content-Type' => 'application/vnd.api+json',
       'Accept' => 'application/vnd.api+json',
   ],

   /*
   |--------------------------------------------------------------------------
   | RSA Public Key Mappings
   |--------------------------------------------------------------------------
   |
   | These are the mappings of clients services to their public key.
   | The array key is the client name, and the value is a path from
   | the local storage folder to the public key.
   |
   | Default: none
   | Example: 'client_name' => '/certs/client-public.key',
   */

   'rsa_key_mappings' => [
       //
   ],

];
