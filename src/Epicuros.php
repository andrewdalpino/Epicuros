<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Exceptions\SigningKeyNotFoundException;
use AndrewDalpino\Epicuros\Exceptions\InvalidSigningAlgorithmException;
use AndrewDalpino\Epicuros\Exceptions\UnauthorizedException;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

class Epicuros
{
    const DEFAULT_EXPIRY = 60; // In seconds.
    const BEARER_PREFIX = 'Bearer ';

    /**
     * The singing key identifier.
     *
     * @var  string  $keyId
     */
    protected $keyId;

    /**
     * The signing key.
     *
     * @var  string  $key
     */
    protected $key;

    /**
     * The algorithm to use when signing tokens.
     *
     *  @var  string  $algorithm
     */
    protected $algorithm;

    /**
     * The verifying keys.
     *
     * @var  VerifyingKeyRepository  $verifyingKeys
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
     * @param  string  $keyId
     * @param  string  $key
     * @param  string  $algorithm
     * @param  VerifyingKeyRepository  $verifyingKeys
     * @param  array  $options
     * @throws SigningKeyNotFoundException
     * @throws InvalidSigningAlgorithmException
     * @return void
     */
    public function __construct(string $keyId, string $key, string $algorithm, VerifyingKeyRepository $verifyingKeys, array $options = [])
    {
        if (is_file($key)) {
            $key = file_get_contents($key);
        }

        if ($key === null) {
            throw new SigningKeyNotFoundException();
        }

        if (! in_array($algorithm, $this->allowedAlgorithms)) {
            throw new InvalidSigningAlgorithmException();
        }

        $this->keyId = $keyId;
        $this->key = $key;
        $this->algorithm = $algorithm;
        $this->verifyingKeys = $verifyingKeys;
        $this->expire = $options['expire'] ?? self::DEFAULT_EXPIRY;
    }

    /**
     * Return a bearer token.
     *
     * @param  Context|null $context
     * @return string
     */
    public function generateBearer(Context $context = null) : string
    {
        return self::BEARER_PREFIX . $this->generateToken($context);
    }

    /**
     * Generate a signed token.
     *
     * @param  Context|null  $context
     * @return string
     */
    public function generateToken(Context $context = null) : string
    {
        $claims = [
            'jti' => $this->generateRandomUuid(),
            'exp' => time() + $this->expire,
            'iat' => time(),
        ];

        if (! is_null($context)) {
            $claims = array_merge($context->toArray(), $claims);
        }

        $token = JWT::encode($claims, $this->key, $this->algorithm, $this->keyId);

        return $token;
    }

    /**
     * Authorize the message.
     *
     * @param  string  $token
     * @throws UnauthorizedException
     * @return void
     */
    public function authorize(string $token = null)
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
    public function acquireContext(string $token = null) : Context
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
    protected function verifyToken(string $token = null) : array
    {
        return JWT::decode($token, $this->verifyingKKeys, $this->allowedAlgorithms);
    }

    /**
     * @return string
     */
    protected function generateRandomUuid() : string
    {
        return Uuid::uuid4()->toString();
    }
}
