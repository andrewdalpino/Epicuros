<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Traits\MagicGetters;
use JsonSerializable;

class Context implements JsonSerializable
{
    use MagicGetters;

    /**
     * Any additional claims.
     *
     * @var  array  $claims
     */
    protected $claims = [
        //
    ];

    /**
     * @param  string|null  $subject
     * @param  array|null  $scopes
     * @param  array|null  $permissions
     * @param  bool|null  $verified
     * @return self
     */

    public static function build(string $subject = null, array $scopes = [], array $permissions = [], bool $verified = null)
    {
        return new self($subject, $scopes, $permissions, $verified);
    }

    /**
     * Reconstiute the context from claims.
     *
     * @param  array  $claims
     * @return self
     */
    public static function reconstitute(array $claims)
    {
        $context = new self();

        return $context->withClaims($claims);
    }

    /**
     * Constructor.
     *
     * @param  string|null  $subject
     * @param  array|null  $scopes
     * @param  array|null  $permissions
     * @param  bool|null  $verified
     * @param  string|null  $ip
     * @return void
     */
    public function __construct(string $subject = null, array $scopes = [], array $permissions = [], bool $verified = null)
    {
        $this->claims['sub'] = $subject;
        $this->claims['scopes'] = $scopes;
        $this->claims['permissions'] = $permissions;
        $this->claims['verified'] = $verified;
    }

    /**
     * @param  string  $ip
     * @return self
     */
    public function withIp(string $ip)
    {
        $this->claims['ip'] = $ip;

        return $this;
    }

    /**
     * Include any custom claims.
     *
     * @param  array  $claims
     * @return self
     */
    public function withClaims(array $claims)
    {
        $this->claims = array_merge($this->claims, $claims);

        return $this;
    }

    /**
     * Does the client have authorization to a particular scope?
     *
     * @param  string  $scope
     * @return bool
     */
    public function hasScope(string $scope) : bool
    {
        return in_array($scope, $this->claims['scopes']);
    }

    /**
     * Does the viewer have a specific permission?
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasPermission(string $permission) : bool
    {
        return in_array($permission, $this->claims['permissions']);
    }

    /**
     * @return string|null
     */
    public function getViewerId() : ?string
    {
        return $this->getSubject();
    }

    /**
     * @return string|null
     */
    public function getSubject() : ?string
    {
        return $this->claims['sub'] ?? null;
    }

    /**
     * @return array
     */
    public function getScopes() : array
    {
        return $this->claims['scopes'] ?? [];
    }

    /**
     * @return array
     */
    public function getPermissions() : array
    {
        return $this->claims['permissions'] ?? [];
    }

    /**
     * @return bool|null
     */
    public function getVerified() : ?bool
    {
        return $this->claims['verified'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getIp() : ?string
    {
        return $this->claims['ip'] ?? null;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->claims ?? [];
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this);
    }
}
