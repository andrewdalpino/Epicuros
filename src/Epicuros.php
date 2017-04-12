<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\InvalidSigningAlgorithmException;
use AndrewDalpino\Epicuros\SigningKeyNotFoundException;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
     * The public key mappings of all the client services.
     *
     * @var  array  $publicKeys
     */
    protected $publicKeys;

    /**
     * The algorithm to use when signing and verifying JWTs.
     *
     *  @var  string  $algorithm
     */
    protected $algorithm;

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
     * @var  array  allowedAlgorithms
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
     * @return string
     */
    protected function generateUuid() : string
    {
        return Uuid::uuid4()->toString();
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
     * @param  string  $jwt
     * @return string|null
     */
    public function getVerifyingKey(string $jwt = null) : ?string
    {
        $name = $this->getIssuer($jwt);

        $key = array_filter($this->publicKeys, function ($value, $key) {
            return $key === $name;
        }, ARRAY_FILTER_USE_BOTH);

        if ($this->algorithm === 'RS256') {
            try {
                return file_get_contents(storage_path($key));
            } catch (\Exception $e) {
                throw new SigningKeyNotFoundException();
            }
        }

        return $key ?? null;
    }

    /**
     * Get the issuer of the JWT token.
     *
     * @param  string  $jwt
     * @return string|null
     */
    public function getIssuer(string $jwt) : ?string
    {
        return $this->getClaims($jwt)['iss'];
    }

    /**
     * @return string
     */
    public function getAlgorithm() ? string
    {
        return $this->algorithm;
    }

    /**
     * @param  string  $jwt
     * @return array|null
     */
    public function getHeader(string $jwt) ?array
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $jwt)[0]));
    }

    /**
     * @param  string  $jwt
     * @return array|null
     */
    public function getClaims(string $jwt) ?array
    {
        return json_decode(JWT::urlsafeB64Decode(explode('.', $jwt)[1]));
    }
}
