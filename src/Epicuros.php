<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Exceptions\InvalidTokenException;
use AndrewDalpino\Epicuros\Exceptions\InvalidSigningAlgorithmException;
use AndrewDalpino\Epicuros\Exceptions\VerifyingKeyNotFoundException;
use AndrewDalpino\Epicuros\Exceptions\ServerUnauthorizedException;
use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;

class Epicuros
{
    const BEARER_PREFIX = 'Bearer ';

    /**
     * The identifier of the token issuer.
     *
     * @var  string  $issuer
     */
    protected $issuer;

    /**
     * The key used to sign tokens.
     *
     * @var  string  $key
     */
    protected $key;

    /**
     * The algorithm to use when signing and verifying JWTs.
     *
     *  @var  string  $algorithm
     */
    protected $algorithm;

    /**
     * The time in seconds before the token expires.
     *
     * @var  int  $expire
     */
    protected $expire;

    /**
     * The public/shared key mappings of all the services.
     *
     * @var  array  $publicKeys
     */
    protected $publicKeys = [
        //
    ];

    /**
     * The allowed signing algorithms.
     *
     * @var  array  $allowedAlgorithms
     */
    protected $allowedAlgorithms = [
        'RS256', 'HS256', 'HS384', 'HS512',
    ];

    /**
     * Constructor.
     *
     * @param  string  $issuer
     * @param  string  $key
     * @param  string  $algorithm
     * @param  int  $expire
     * @param  array  $publicKeys
     * @return void
     */
    public function __construct(string $issuer, string $key, string $algorithm, int $expire, array $publicKeys = [])
    {
        if (! in_array($algorithm, $this->allowedAlgorithms)) {
            throw new InvalidSigningAlgorithmException();
        }

        $this->issuer = $issuer;
        $this->key = $key;
        $this->algorithm = $algorithm;
        $this->expire = $expire;
        $this->publicKeys = $publicKeys;
    }

    /**
     * Generate a bearer token.
     *
     * @param  Context|null $context
     * @return string
     */
    public function bearer(Context $context = null) : string
    {
        return self::BEARER_PREFIX . $this->generateToken($context);
    }

    /**
     * Return a signed JWT.
     *
     * @param  Context|null  $context
     * @return string
     */
    public function generateToken(Context $context = null) : string
    {
        $claims = [
            'jti' => $this->generateRandomUuid(),
            'iss' => $this->issuer,
            'exp' => time() + (int) $this->expire,
            'iat' => time(),
        ];

        if (! is_null($context)) {
            $claims = array_merge($claims, $context->toArray());
        }

        return JWT::encode($claims, $this->key, $this->algorithm);
    }

    /**
     * Verify the token and extract the claims.
     *
     * @param  string  $jwt
     * @return Context
     */
    public function authorize(string $jwt = null)
    {
        if (is_null($jwt)) {
            throw new InvalidTokenException();
        }

        $key = $this->getVerifyingKey($jwt);

        try {
            $claims = JWT::decode($jwt, $key, [$this->algorithm]);
        } catch (\Exception $e) {
            throw new ServerUnauthorizedException();
        }

        return $this->acquireContext($claims);
    }

    /**
     * Acquire the context from claims.
     *
     * @param  stdClass  $claims
     * @return Context
     */
    public function acquireContext($claims)
    {
        return Context::reconstitute((array) $claims);
    }

    /**
     * @param  string  $jwt
     * @return string
     */
    public function getVerifyingKey(string $jwt) : string
    {
        $issuer = $this->getTokenIssuer($jwt);

        foreach ($this->publicKeys as $name => $key) {
            if ($name === $issuer) {
                $verifyingKey = $key;
            }
        }

        if ($this->algorithm === 'RS256') {
            try {
                $verifyingKey = file_get_contents(storage_path($verifyingKey));
            } catch (\Exception $e) {
                $verifyingKey = null;
            }
        }

        if (! isset($verifyingKey) || empty($verifyingKey)) {
            throw new VerifyingKeyNotFoundException();
        }

        return $verifyingKey;
    }

    /**
     * Get the issuer of the JWT token.
     *
     * @param  string  $jwt
     * @return string|null
     */
    public function getTokenIssuer(string $jwt) : ?string
    {
        return $this->getTokenClaims($jwt)->iss ?? null;
    }

    /**
     * @param  string  $jwt
     * @return object|null
     */
    public function getTokenHeader(string $jwt)
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $jwt)[0] ?? null));
    }

    /**
     * @param  string  $jwt
     * @return object|null
     */
    public function getTokenClaims(string $jwt)
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $jwt)[1] ?? null));
    }

    /**
     * @param  string  $jwt
     * @return string|null
     */
    public function getTokenSignature(string $jwt) : ?string
    {
        return JWT::urlsafeB64Decode(explode('.', $jwt)[2] ?? null);
    }

    /**
     * @return string
     */
    protected function generateRandomUuid() : string
    {
        return Uuid::uuid4()->toString();
    }
}
