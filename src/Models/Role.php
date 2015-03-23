<?php namespace Dencker\Watchtower\Models;

/**
 * Class Role
 *
 * @package Dencker\Watchtower\Models
 * @property string $code
 * @property bool   $is_super_user
 * @property string $name
 *
 *
 * @todo Define properties up here!
 */

class Role extends AbstractWatchtowerModel
{

    protected $fillable = ['name', 'code', 'is_super_user'];
    protected $table = "watchtower_roles";
    protected static $primaryActor = null;

    public static function getPrimaryActor()
    {
        return self::$primaryActor;
    }

    public static function setPrimaryActor($primaryActor)
    {
        $actor = null;

        if ( is_string( $primaryActor ) )
        {
            if ( !class_exists( $primaryActor ) )
                throw new \Exception( "Class \"{$primaryActor}\" must exist" );

            $actor = $primaryActor;
        }

        if ( is_null( $actor ) )
            throw new \Exception( "Actor must be a string" );

        self::$primaryActor = $actor;
    }

    public function permissions()
    {
        return $this->belongsToMany( 'Dencker\Watchtower\Models\Permission', 'watchtower_roles_permissions' );
    }

    public function actors($related = null)
    {
        if ( is_null( $related ) )
            $related = self::getPrimaryActor();

        return $this->morphedByMany( $related, 'actor', 'watchtower_actors' );
    }


}