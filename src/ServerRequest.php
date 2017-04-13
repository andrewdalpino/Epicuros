<?php

namespace AndrewDalpino\Epicuros;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

class ServerRequest extends Request
{
    const HTTP_PREFIX = 'http://';
    const HTTPS_PREFIX = 'https://';

    /**
     * The address of the server.
     *
     * @var  string  $server
     */
    protected $server;

    /**
     * The uri of the resource.
     *
     * @var  string  $resource
     */
    protected $resource;

    /**
     * The input parameters.
     *
     * @var  array  $params
     */
    protected $params = [
        //
    ];

    /**
     * @var  array  $includes
     */
    protected $includes = [
        //
    ];

    /**
     * @var  Context  $context
     */
    protected $context;

    /**
     * @var  Cursor  $cursor
     */
    protected $cursor;

    /**
     * Should the request be made over HTTPS?
     *
     * @var  bool  $secure
     */
    protected $secure;

    /**
     * The allowed HTTP methods.
     *
     * @var  array  $allowedMethods
     */
    protected $allowedMethods = [
        'GET', 'PUT', 'POST', 'DELETE', 'PATCH', 'HEAD',
    ];

    /**
     * Constructor.
     *
     * @param  string|null $method
     * @param  string|null  $uri
     * @return void
     */
    public function __construct(string $method = null, string $uri = null, array $headers = [], string $body = null, $version = '1.1')
    {
        $this->secure = true;

        parent::__construct($method, $uri, $headers, $body, $version);
    }

    /**
     * Use this resource server to handle the request.
     *
     * @param  string  $server
     * @return self
     */
    public function use(string $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Set the HTTP method.
     *
     * @param  string  $method
     * @throws \App\Services\Support\Epicuros\HttpMethodNotAllowedException
     * @return self
     */
    public function setHttpMethod(string $method)
    {
        $method = strtoupper($method);

        if (! in_array($method, $this->allowedMethods)) {
            throw new HttpMethodNotAllowedException;
        }

        return $this->withMethod($method);
    }

    /**
     * Operate on this resource.
     *
     * @param  string  $uri
     * @param  array  $parameters
     * @return self
     */
    public function resource(string $resource, ...$parameters)
    {
        $this->resource = $this->hydrateUri($resource, $parameters);

        return $this;
    }

    /**
     * Set the input parameters of the request.
     *
     * @param  array  $params
     * @return self
     */
    public function withParams(array $params)
    {
        $this->params = array_merge($params, $this->params);

        return $this;
    }

    /**
     * @param  array  $includes
     * @return self
     */
    public function withIncludes(...$includes)
    {
        $this->includes = array_merge($includes, $this->includes);

        return $this;
    }

    /**
     * @param  Context  $context
     * @return self
     */
    public function withContext(Context $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @param  Cursor  $cursor
     * @return self
     */
    public function withCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;

        return $this;
    }

    /**
     * Force the request over unencrypted HTTP.
     *
     * @return self
     */
    public function insecure()
    {
        $this->secure = false;

        return $this;
    }

    /**
     * @return \Psr\Http\Message\Uri
     */
    public function getUri()
    {
        $prefix = $this->secure ? self::HTTPS_PREFIX : self::HTTP_PREFIX;

        return new Uri($prefix . $this->server . $this->resource);
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Cursor
     */
    public function getCursor()
    {
        return $this->cursor->getOffset() . ',' . $this->cursor->getPrevious() . ',' . $this->cursor->getLimit();
    }

    /**
     * @return string|null
     */
    public function getIncludes() : ?string
    {
        return implode(',', $this->includes);
    }

    /**
    * Get the JSON body of the request.
    *
    * @return string
    */
   public function getJsonBody() : ?string
   {
       $body = [];

       if ($this->hasParams()) {
           $body[config('epicuros.params_prefix', 'params')] = $this->params;
       }

       return json_encode($body);
   }

   /**
    * Hydrate the uniform resource identifier with parameters.
    *
    * @param  string  $uri
    * @param  array  $parameters
    * @return string
    */
   public function hydrateUri(string $uri, array $parameters) : string
   {
       foreach ($parameters as $parameter) {
           $uri = preg_replace('/[\[{\(].*[\]}\)]/U', (string) $parameter, $uri, 1);
       }

       return $uri;
   }

    /**
     * Does the current request have includes?
     *
     * @return bool
     */
    public function hasIncludes() : bool
    {
        return ! empty($this->includes);
    }

    /**
     * Does the request have input params?
     *
     * @return bool
     */
    public function hasParams() : bool
    {
        return ! empty($this->params);
    }

    /**
     * Does the current request have context?
     *
     * @return bool
     */
    public function hasContext() : bool
    {
        return ! is_null($this->context);
    }

    /**
     * Does the request have a cursor?
     *
     * @return bool
     */
    public function hasCursor() : bool
    {
        return ! is_null($this->cursor);
    }

    /**
     * Set the HTTP method to GET.
     *
     * @return self
     */
    public function get()
    {
        return $this->setHttpMethod('GET');
    }

    /**
     * Set the HTTP method to PUT.
     *
     * @return self
     */
    public function put()
    {
        return $this->setHttpMethod('PUT');
    }

    /**
     * Set the HTTP method to PATCH.
     *
     * @return self
     */
    public function patch()
    {
        return $this->setHttpMethod('PATCH');
    }

    /**
     * Set the HTTP method to DELETE.
     *
     * @return self
     */
    public function delete()
    {
        return $this->setHttpMethod('DELETE');
    }

    /**
     * Set the HTTP method to POST.
     *
     * @return self
     */
    public function post()
    {
        return $this->setHttpMethod('POST');
    }

    /**
     * Set the HTTP method to HEAD.
     *
     * @return self
     */
    public function head()
    {
        return $this->setHttpMethod('HEAD');
    }
}
