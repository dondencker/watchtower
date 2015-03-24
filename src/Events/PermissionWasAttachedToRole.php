<?php  namespace Dencker\Watchtower\Events; 

use Dencker\Watchtower\Models\Permission;
use Dencker\Watchtower\Models\Role;
use Illuminate\Queue\SerializesModels;

class PermissionWasAttachedToRole {

    use SerializesModels;
    /**
     * @var Permission
     */
    public $permission;
    /**
     * @var Role
     */
    public $role;

    function __construct(Permission $permission, Role $role)
    {
        $this->permission = $permission;
        $this->role = $role;
    }
}