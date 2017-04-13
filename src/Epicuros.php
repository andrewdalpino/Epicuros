<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\InvalidSigningAlgorithmException;
use AndrewDalpino\Epicuros\SigningKeyNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\ClientInterface;
use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;

class Epicuros
{
    const BEARER_PREFIX = 'Bearer ';

    /**
     * The HTTP client.
     *
     * @var  \GuzzleHttp\ClientInterface  $client
     */
    protected $client;

    /**
     * The key used to sign the requests made to the server.
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
     * The number of seconds before a token expires.
     *
     * @var  int  $expire
     */
    protected $expire;

    /**
     * The public/shared key mappings of all the services.
     *
     * @var  array  $publicKeys
     */
    protected $publicKeys;

    /**
     * The queued server requests.
     *
     * @var  array  $queue
     */
    protected $queue = [
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
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @param  string  $algorithm
     * @param  int  $expire
     * @param  array  $publicKeys
     * @return void
     */
    public function __construct(ClientInterface $client, string $key, string $algorithm, int $expire, array $publicKeys = [])
    {
        if (! in_array($algorithm, $this->allowedAlgorithms)) {
            throw new InvalidSigningAlgorithmException();
        }

        $this->client = $client;
        $this->key = $key;
        $this->algorithm = $algorithm;
        $this->expire = $expire;
        $this->publicKeys = $publicKeys;
    }

    /**
     * Create a new server request.
     *
     * @return ServerRequest
     */
    public static function createServerRequest()
    {
        return new ServerRequest();
    }

    /**
     * Send a single request to the server.
     *
     * @param  ServerRequest  $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(ServerRequest $request)
    {
         return $this->client->send($request, $this->getOptions($request));
    }

    /**
     * Queue a server request.
     *
     * @param  ServerRequest  $request
     * @return self
     */
    public function queue(ServerRequest $request)
    {
        $this->queue[] = $request;

        return $this;
    }

    /**
     * Execute the queued server requests concurrently.
     *
     * @return array
     */
    public function execute()
    {
        // TODO
    }

    /**
     * Verify the token and extract the claims.
     *
     * @param  string  $jwt
     * @return stdClass
     */
    public function authorize(string $jwt)
    {
        try {
            $claims = JWT::decode($jwt, $this->getVerifyingKey($jwt), [$this->getAlgorithm()]);
        } catch (\Exception $e) {
            throw new ServiceUnauthorizedException();
        }

        return $this->acquireContext($claims);
    }

    /**
     * Get the request options.
     *
     * @param  ServerRequest  $request
     * @return array
     */
    protected function getOptions(ServerRequest $request) : array
    {
        return [
            'headers' => [
                'Authorization' => $this->getBearer($request),
            ],
            'body' => $this->getBody($request),
        ];
    }

    /**
     * Return a signed JWT bearer token.
     *
     * @param  ServerRequest  $request
     * @return string
     */
    protected function getBearer(ServerRequest $request) : string
    {
        $claims = [
            'jti' => $this->generateUuid(),
            'iss' => config('epicuros.client_name', 'Epicuros'),
            'exp' => time() + intval($this->expire),
            'iat' => time(),
        ];

        if ($request->hasContext()) {
            $context = $request->getContext();

            array_merge([
                'sub' => $context->getViewerId(),
                'scopes' => $context->getScopes(),
                'permissions' => $context->getPermissions(),
                'verified' => $context->getVerified(),
                'ip' => $context->getIp(),
            ], $claims);
        }

        return self::BEARER_PREFIX . JWT::encode($claims, $this->key, $this->algorithm);
    }

    /**
     * Get the JSON body of the request.
     *
     * @param  ServerRequest $request
     * @return string
     */
    protected function getBody(ServerRequest $request) : ?string
    {
        $body = [];

        if ($request->hasParams()) {
            $body['params'] = $request->getParams();
        }

        if ($request->hasCursor()) {
            $body['meta']['cursor'] = $request->getCursor()->toArray();
        }

        return json_encode($body);
    }

    /**
     * Acquire the context from
     *
     * @param  string  $jwt
     * @return Context
     */
    public function acquireContext($claims)
    {
        return new Context(
            $claims->sub ?? null,
            $claims->scopes ?? [],
            $claims->permissions ?? [],
            $claims->verified ?? false,
            $claims->ip ?? null
        );
    }

    /**
     * @param  string  $jwt
     * @return string
     */
    public function getVerifyingKey(string $jwt) : ?string
    {
        $issuer = $this->getIssuer($jwt);

        foreach ($this->publicKeys as $k => $v) {
            if ($k === $issuer) {
                $key = $v;
            }
        }

        if ($this->getAlgorithm() === 'RS256') {
            try {
                $key = file_get_contents(storage_path($key));
            } catch (\Exception $e) {
                $key = null;
            }
        }

        if (is_null($key)) {
            throw new SigningKeyNotFoundException();
        }

        return $key;
    }

    /**
     * @return string
     */
    public function getAlgorithm() : string
    {
        return $this->algorithm;
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
    protected function generateUuid() : string
    {
        return Uuid::uuid4()->toString();
    }
}
