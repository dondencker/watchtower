<?php

namespace Dencker\Watchtower\Traits;
use Dencker\Watchtower\Watchtower;


/**
 * Class HasPermissions
 * @package Dencker\Watchtower\Traits
 */
trait HasPermissions
{

    protected $watchtower = null;

    /**
     * @return Watchtower
     */
    public function watchtower()
    {
        return $this->watchtower ?: new Watchtower($this);
    }

    public function isSuperUser()
    {
        return !! $this->watchtower()->roles->filter(function($role){return $role->is_super_user; })->count();
    }

    public function can($permission)
    {
        return $this->watchtower()->hasPermission($permission,true);
    }

}