<?php namespace spec\stubs;

use Dencker\Watchtower\Contracts\WatchtowerActorContract;
use Dencker\Watchtower\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model implements WatchtowerActorContract
{
    use HasPermissions;
}