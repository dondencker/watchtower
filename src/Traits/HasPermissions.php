<?php namespace Dencker\Watchtower\Traits;


use Dencker\Watchtower\Watchtower;

trait HasPermissions
{

    protected $watchtower = null;

    /**
     * Returns this actors watchtower instance.
     *
     * @return Watchtower
     */
    public function watchtower()
    {

        return $this->watchtower ?: $this->watchtower = app('watchtower',[$this]);
    }


    /**
     * Checks whether the current actor is a super user
     *
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->watchtower()->isSuperUser();
    }


    /**
     * Alias for hasPermission()
     *
     * @param string $permission_code
     *
     * @return bool
     *
     */
    public function can($permission_code)
    {
        return $this->hasPermission($permission_code);
    }

    /**
     * Alias for hasPermission()
     *
     * @param string $permission_code
     *
     * @return bool
     */
    public function hasAccess($permission_code)
    {
        return $this->hasPermission($permission_code);
    }

    /**
     * Checks whether this actor has the given permission
     *
     * @param string $permission_code
     *
     * @return bool
     */
    public function hasPermission($permission_code)
    {
        return $this->watchtower()->hasPermission($permission_code);
    }

}