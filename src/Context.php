<?php

namespace AndrewDalpino\Epicuros;

use JsonSerializable;

class Context implements JsonSerializable
{
    /**
     * The subject/viewer identifier.
     *
     * @var  string  $sub
     */
    protected $sub;

    /**
     * The scopes granted to the client.
     *
     * @var  array  $scopes
     */
    protected $scopes;

    /**
     * The permissions granted to a user.
     *
     * @var  array  $permissions
     */
    protected $permissions;

    /**
     * Is the identity of the viewer verified?
     *
     * @param  bool  $verified
     */
    protected $verified;

    /**
     * The IP address of the client.
     *
     * @var  string  $ip
     */
    protected $ip;

    /**
     * Any additional claims.
     *
     * @var  array  $claims
     */
    protected $claims = [
        //
    ];

    /**
     * @param  string|null  $sub
     * @param  array|null  $scopes
     * @param  array|null  $permissions
     * @param  bool|null  $verified
     * @param  string|null  $ip
     * @return self
     */

    public static function build(string $sub = null, array $scopes = [], array $permissions = [], bool $verified = null, string $ip = null)
    {
        return new self($sub, $scopes, $permissions, $verified, $ip);
    }

    /**
     * Constructor.
     *
     * @param  string|null  $sub
     * @param  array|null  $scopes
     * @param  array|null  $permissions
     * @param  bool|null  $verified
     * @param  string|null  $ip
     * @return void
     */
    public function __construct(string $sub = null, array $scopes = [], array $permissions = [], bool $verified = null, string $ip = null)
    {
        $this->sub = $sub;
        $this->scopes = $scopes;
        $this->permissions = $permissions;
        $this->verified = $verified;
        $this->ip = $ip;
    }

    /**
     * Include any additional custom claims.
     *
     * @param  array  $claims
     * @return self
     */
    public function withClaims(array $claims)
    {
        $this->claims = array_merge($claims, $this->claims);

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
        return in_array($scope, $this->scopes);
    }

    /**
     * Does the viewer have a specific permission?
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasPermission(string $permission) : bool
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * @return string
     */
    public function getSub() : ?string
    {
        return $this->sub;
    }

    /**
     * @return string
     */
    public function getViewer() : ?string
    {
        return $this->getSub();
    }

    /**
     * @return array
     */
    public function getPermissions() : array
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function getScopes() : array
    {
        return $this->scopes;
    }

    /*
     * @return boolean
     */
    public function getVerified() : ?bool
    {
        return $this->verified;
    }

    /**
     * @return string
     */
    public function getIp() : ?string
    {
        return $this->ip;
    }

    /**
     * @return array
     */
    public function getClaims() : ?array
    {
        return $this->claims;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return array_merge([
            'sub' => $this->getSub(),
            'scopes' => $this->getScopes(),
            'permissions' => $this->getPermissions(),
            'verified' => $this->getVerified(),
            'ip' => $this->getIp(),
        ], $this->getClaims());
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
