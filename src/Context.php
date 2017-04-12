<?php

namespace AndrewDalpino\Epicuros;

class Context
{
    /**
     * The viewer identifier.
     *
     * @var  string  $viewerId
     */
    protected $viewerId;

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
     * Constructor.
     *
     * @param  string|null  $viewerId
     * @param  array|null  $scopes
     * @param  array|null  $permissions
     * @param  bool|null  $verified
     * @param  string|null  $ip
     * @return void
     */
    public function __construct(string $viewerId =  null, array $scopes = [], array $permissions = [], bool $verified = null, string $ip = null)
    {
        $this->viewerId = $viewerId;
        $this->scopes = $scopes;
        $this->permissions = $permissions;
        $this->verified = $verified;
        $this->ip = $ip;
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
    public function getViewerId() : ?string
    {
        return $this->viewerId;
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
}
