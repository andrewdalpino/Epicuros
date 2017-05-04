<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Exceptions\SigningKeyNotFoundException;
use AndrewDalpino\Epicuros\Exceptions\InvalidSigningAlgorithmException;
use AndrewDalpino\Epicuros\Exceptions\UnauthorizedException;
use Firebase\JWT\JWT;

class Epicuros
{
    const JTI_LENGTH = 20;
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
     * @var  array  $verifyingKeys
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
     * @param  array  $verifyingKeys
     * @param  array  $options
     * @throws SigningKeyNotFoundException
     * @throws InvalidSigningAlgorithmException
     * @return void
     */
    public function __construct(string $keyId, string $key, string $algorithm, array $verifyingKeys = [], array $options = [])
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
     * @param  array  $claims
     * @return string
     */
    public function generateToken(array $claims = []) : string
    {
        $claims = array_merge($claims, [
            'jti' => $this->generateRandomUuid(),
            'exp' => time() + $this->expire,
            'iat' => time(),
        ]);

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
     * Verify the token and return the decoded claims.
     *
     * @param  string  $token
     * @return array
     */
    public function verifyToken(string $token = null) : array
    {
        return JWT::decode($token, $this->verifyingKeys, $this->allowedAlgorithms);
    }


    /**
     * Return the decoded claims without verifying the token signature.
     *
     * @param  string|null  $token
     * @return array
     */
    public function getTokenClaims(string $token = null)
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $token)[1] ?? null), true);
    }

    /**
     * @return string
     */
    protected function generateTokenIdentifier() : string
    {
        return bin2hex(random_bytes(self::JTI_LENGTH));
    }
}
