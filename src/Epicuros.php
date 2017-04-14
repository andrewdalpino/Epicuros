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
     * The identifier of the service.
     *
     * @var  string  $service
     */
    protected $service;

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
     * The verifying key maps.
     *
     * @var  array  $verifyingKeys
     */
    protected $verifyingKeys = [
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
     * @param  string  $service
     * @param  string  $key
     * @param  string  $algorithm
     * @param  int  $expire
     * @param  array  $publicKeys
     * @return void
     */
    public function __construct(string $service, string $key, string $algorithm, int $expire, array $publicKeys = [])
    {
        if (! in_array($algorithm, $this->allowedAlgorithms)) {
            throw new InvalidSigningAlgorithmException();
        }

        $this->service = $service;
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
     * @param  array  $audience
     * @return string
     */
    public function generateToken(Context $context = null, ...$audience) : string
    {
        $claims = [
            'jti' => $this->generateRandomUuid(),
            'iss' => $this->issuer,
            'exp' => time() + (int) $this->expire,
            'iat' => time(),
        ];

        if (! empty($audience)) {
            $claims['aud'] = $audience;
        }

        if (! is_null($context)) {
            $claims = array_merge($context->toArray(), $claims);
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
        try {
            if (is_null($jwt)) {
                throw new InvalidTokenException();
            }

            $audience = $this->getTokenAudience($jwt);

            if (! empty($audience) && ! in_array($this->service, $audience)) {
                throw new NotIntendedAudienceException();
            }

            $key = $this->getVerifyingKey($jwt);

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
    protected function acquireContext($claims)
    {
        return Context::reconstitute((array) $claims);
    }

    /**
     * @param  string  $jwt
     * @return string
     */
    protected function getVerifyingKey(string $jwt) : string
    {
        $issuer = $this->getTokenIssuer($jwt);

        foreach ($this->verifyingKeys as $service => $verifyingKey) {
            if ($service === $issuer) {
                $key = $verifyingKey;
            }
        }

        if ($this->algorithm === 'RS256') {
            try {
                $key = file_get_contents(storage_path($key));
            } catch (\Exception $e) {
                $key = null;
            }
        }

        if (! isset($key) || empty($key)) {
            throw new VerifyingKeyNotFoundException();
        }

        return $key;
    }

    /**
     * Get the issuer of the token.
     *
     * @param  string  $jwt
     * @return string|null
     */
    protected function getTokenIssuer(string $jwt) : ?string
    {
        return $this->getTokenClaims($jwt)->iss ?? null;
    }

    /**
     * Does the token have an audience?
     *
     * @param  string  $jwt
     * @return bool
     */
    protected function hasAudience(string $jwt) : bool
    {
        return ! empty($this->getTokenAudience($jwt)) ? true : false;
    }

    /**
     * Get the intended audience of the token.
     *
     * @param  string  $jwt
     * @return array
     */
    protected function getTokenAudience(string $jwt) : array
    {
        return $this->getTokenClaims($jwt)->aud ?? [];
    }

    /**
     * @param  string  $jwt
     * @return object|null
     */
    protected function getTokenClaims(string $jwt)
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $jwt)[1] ?? null));
    }

    /**
     * @return string
     */
    protected function generateRandomUuid() : string
    {
        return Uuid::uuid4()->toString();
    }
}
