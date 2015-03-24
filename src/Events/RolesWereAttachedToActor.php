<?php namespace Dencker\Watchtower\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class RolesWereAttachedToActor {

	use SerializesModels;

    /** @var Model */
    public $actor;
    /** @var Collection */
    public $roles;

    /**
     * Create a new event instance.
     *
     * @param Model $actor
     * @param Collection $roles
     */
	public function __construct(Model $actor, Collection $roles)
	{
        $this->actor = $actor;
        $this->roles = $roles;
    }

}
