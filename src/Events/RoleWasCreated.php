<?php namespace Dencker\Watchtower\Events;

use Dencker\Watchtower\Models\Role;
use Illuminate\Queue\SerializesModels;

class RoleWasCreated {

	use SerializesModels;

    /**
     * @var Role
     */
    public $role;

    public function __construct(Role $role)
	{
        $this->role = $role;
    }

}
