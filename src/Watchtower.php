<?php namespace Dencker\Watchtower;

use Dencker\Watchtower\Models\Permission;


use Dencker\Watchtower\Contracts\WatchtowerActorContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Session\SessionInterface as SessionContract;

/**
 * Class Watchtower
 *
 * @package Dencker\Watchtower
 * @property $roles
 * @property $permissions
 */
class Watchtower
{


    /**
     * @var WatchtowerActorContract|Model
     */
    private $actor;

    /**
     * The "properties" on the class which can be accessed through magic methods.
     * Ultimately, a method of the same name is called.
     *
     * @var array
     */
    private $properties = ['roles', 'permissions'];
    /**
     * @var Session
     */
    private $session;


    function __construct(Model $actor, Session $session)
    {
        if ( !$this->modelImplementsWatchtowerContract( $actor ) )
            throw new \ErrorException( "Model must implement Dencker\\Watchtower\\Contracts\\WatchtowerContract" );

        $this->actor          = $actor;
        $this->session        = $session;
    }

    public function clearSession()
    {
        $this->session->clear();
    }


    /**
     * Retrieves a relation, either directly from the model or from the session.
     *
     * @return MorphToMany|Collection|null
     */
    public function roles()
    {
        /*
         * We're storing the relation in a session to minimize the query load.
         */
        if ( !$roles = $this->session->getRoles() )
        {
            $roles = $this->actor->morphToMany( 'Dencker\Watchtower\Models\Role', 'actor', 'watchtower_actors' );
            $this->session->setRoles( $roles->get() );
        }


        return $roles;
    }


    public function permissions()
    {
        if ( !$permissions = $this->session->getPermissions() )
        {
            $permissions = collect();

            foreach ($this->roles as $role)
                foreach ($role->permissions as $permission)
                    $permissions->put( $permission->id, $permission );

            $permissions = $permissions->flatten();

            $this->session->setPermissions( $permissions );
        }

        return $permissions;
    }


    public function hasPermission($permission_code)
    {
        /*
         * If the user is a superuser, he has access to everything, even when the permission doesn't exist
         */
        if ( $this->isSuperUser() )
        {
            return true;
        }

        /*
         * If the permission code passed is actually an instance of the Permission model, grab the code off of that object.
         */
        if ( is_a( $permission_code, 'Dencker\Watchtower\Models\Permission' ) )
        {
            $permission_code = $permission_code->code;
        }

        return in_array( $permission_code, $this->permissions->lists( 'code' ) );

    }




    public function __get($name)
    {
        if ( !in_array( $name, $this->properties ) )
            throw new \Exception( "Property {$name} not found. Available properties are " . join( ",", $this->properties ) );

        $prop = $this->{$name}();

        return is_a( $prop, 'Illuminate\Database\Eloquent\Relations\Relation' ) ? $prop->get() : $prop;
    }


    /**
     * @param Model $actor
     *
     * @return array
     */
    private function modelImplementsWatchtowerContract(Model $actor)
    {
        return class_implements( $actor ) && in_array( 'Dencker\\Watchtower\\Contracts\\WatchtowerActorContract', class_implements( $actor ) );
    }

    /**
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->roles && in_array( true, $this->roles->lists( 'is_super_user' ) );
    }


}