<?php namespace Dencker\Watchtower\Models;


class Permission extends AbstractWatchtowerModel
{
     protected $fillable=['name','code'];
    protected $table = 'watchtower_permissions';
}
