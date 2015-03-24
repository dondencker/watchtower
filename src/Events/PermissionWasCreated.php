<?php  namespace Dencker\Watchtower\Events; 

use Dencker\Watchtower\Models\Permission;
use Illuminate\Queue\SerializesModels;

class PermissionWasCreated {
    use SerializesModels;

    /**
     * @var Permission
     */
    public $permission;

    function __construct(Permission $permission)
    {

        $this->permission = $permission;
    }
}