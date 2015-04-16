<?php namespace Dencker\Watchtower\Models;

class WatchtowerActor extends AbstractWatchtowerModel
{
    public $timestamps = false;
    protected $fillable = ['role_id','actor_id','actor_type'];
}