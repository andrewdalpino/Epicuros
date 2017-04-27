<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Exceptions\InvalidSigningAlgorithmException;
use AndrewDalpino\Epicuros\Exceptions\UnauthorizedException;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

class Epicuros
{
    const DEFAULT_EXPIRY = 60; // In seconds.
    const BEARER_PREFIX = 'Bearer ';

    /**
     * The algorithm to use when signing tokens.
     *
     *  @var  string  $algorithm
     */
    protected $algorithm;

    /**
     * The signing keys.
     *
     * @var  KeyRepository  $signingKeys
     */
    protected $signingKeys;

    /**
     * The verifying keys.
     *
     * @var  KeyRepository  $verifyingKeys
     */
    protected $verifyingKeys;

    /**
     * The token expiry.
     *
     * @var  int  $expire
     */
    protected $expire;

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
     * @param  string  $algorithm
     * @param  KeyRepository  $signingKeys
     * @param  KeyRepository  $verifyingKeys
     * @param  array  $options
     * @return void
     */
    public function __construct(string $algorithm, KeyRepository $signingKeys, KeyRepository $verifyingKeys, array $options = [])
    {
        if (! in_array($algorithm, $this->allowedAlgorithms)) {
            throw new InvalidSigningAlgorithmException();
        }

        $this->algorithm = $algorithm;
        $this->signingKeys = $signingKeys;
        $this->verifyingKeys = $verifyingKeys;
        $this->expire = $options['expire'] ?? self::DEFAULT_EXPIRY;
    }

    /**
     * Return a bearer token.
     *
     * @param  mixed|null  $keyId
     * @param  Context|null $context
     * @return string
     */
    public function generateBearer($keyId = null, Context $context = null) : string
    {
        return self::BEARER_PREFIX . $this->generateToken($keyId, $context);
    }

    /**
     * Generate a signed token.
     *
     * @param  mixed|null  $keyId
     * @param  Context|null  $context
     * @return string
     */
    public function generateToken($keyId = null, Context $context = null) : string
    {
        if ($keyId === null) {
            $key = $this->signingKeys->first();
        } else {
            $key = $this->signingKeys->fetch($keyId);
        }

        $claims = [
            'jti' => $this->generateRandomUuid(),
            'exp' => time() + $this->expire,
            'iat' => time(),
        ];

        if (! is_null($context)) {
            $claims = array_merge($context->toArray(), $claims);
        }

        return JWT::encode($claims, $key, $this->algorithm, $keyId);
    }

    /**
     * Authorize the message.
     *
     * @param  string  $token
     * @throws UnauthorizedException
     * @return void
     */
    public function authorize(string $token)
    {
        try {
            $this->verifyToken($token);
        } catch (\Exception $e) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @param  string  $token
     * @return Context
     */
    protected function acquireContext(string $token) : Context
    {
        $claims = $this->verifyToken($token);

        return Context::build($claims);
    }

    /**
     * Verify the token and return the decoded claims.
     *
     * @param  string  $token
     * @return array
     */
    protected function verifyToken(string $token) : array
    {
        return JWT::decode($token, $this->verifyingKKeys, $this->allowedAlgorithms);
    }

    /**
     * @param  string  $token
     * @return string|null
     */
    protected function getTokenIssuer(string $token) : ?string
    {
        return $this->getTokenClaims($token)['iss'] ?? null;
    }

    /**
     * @param  string  $token
     * @return array
     */
    protected function getTokenClaims(string $token) : array
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $token)[1] ?? []), true);
    }

    /**
     * @return string
     */
    protected function generateRandomUuid() : string
    {
        return Uuid::uuid4()->toString();
    }
}
