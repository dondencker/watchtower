<?php namespace Dencker\Watchtower;

use Illuminate\Session\SessionInterface;
use Illuminate\Session\SessionManager;

class Session
{


    /** @var SessionInterface */
    private $session;

    /** @var string  */
    private $prefix;

    function __construct(SessionManager $session, $prefix = "watchtower.")
    {
        $this->session = $session->driver();
        $this->prefix  = $prefix;
    }

    public function clear()
    {
        $keys = [
            $this->getPermissionsSessionKey(),
            $this->getRolesSessionKey(),
        ];

        foreach ($keys as $session_identifier)
            $this->session->remove( $session_identifier );
    }

    public function getRoles()
    {
        return $this->session->get($this->getRolesSessionKey());
    }

    /**
     * @return string
     */
    public function getPermissionsSessionKey()
    {
        return $this->sessionKey( 'permissions' );
    }

    /**
     * @return string
     */
    public function getRolesSessionKey()
    {
        return $this->sessionKey( 'roles' );
    }

    private function sessionKey($identifier)
    {
        return $this->prefix . $identifier;
    }

    public function setRoles($roles)
    {
        return $this->set($this->getRolesSessionKey(), $roles);
    }

    private function set($key, $value)
    {
        $this->session->set($key, $value);
        return $this->session->save();
    }

    public function getPermissions()
    {
        return $this->session->get($this->getPermissionsSessionKey());
    }

    public function setPermissions($value)
    {
        return $this->set($this->getPermissionsSessionKey(), $value);

    }
}