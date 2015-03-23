<?php namespace Dencker\Watchtower\Models;

/**
 * Class Role
 * @package Dencker\Watchtower\Models
 * @property string $code
 * @property bool $is_super_user
 * @property string $name
 *
 *
 * @todo Define properties up here!
 */

class Role extends AbstractWatchtowerModel
{

     protected $fillable = ['name', 'code', 'is_super_user'];
     protected $table = "watchtower_roles";

     public function permissions()
     {
          return $this->belongsToMany( 'Dencker\Watchtower\Models\Permission', 'watchtower_roles_permissions' );
     }

     public function actors($related)
     {
          return $this->morphedByMany($related, 'actor', 'watchtower_actors');
     }


}