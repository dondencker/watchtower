<?php
    namespace Dencker\Watchtower;


    use DB;
    use Dencker\Watchtower\Events\PermissionWasAttachedToRole;
    use Dencker\Watchtower\Events\PermissionWasCreated;
    use Dencker\Watchtower\Events\PermissionWasRemovedFromRole;
    use Dencker\Watchtower\Events\RolesWereAttachedToActor;
    use Dencker\Watchtower\Events\RoleWasCreated;
    use Dencker\Watchtower\Models\Permission;
    use Dencker\Watchtower\Models\Role;
    use Dencker\Watchtower\Models\WatchtowerActor;
    use Illuminate\Contracts\Events\Dispatcher;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\DatabaseManager;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Str;

    class WatchtowerRepository
    {
        /** @var \Illuminate\Database\Connection */
        protected $db;
        /**
         * @var Dispatcher
         */
        private $events;

        /**
         * @param DatabaseManager $db
         * @param Dispatcher      $events
         */
        function __construct(DatabaseManager $db, Dispatcher $events)
        {
            $this->db     = $db->connection();
            $this->events = $events;
        }

        /**
         * Creates a role.
         *
         * @param       $code
         * @param       $name
         * @param bool  $is_super_user
         * @param array $permission_codes An array of permission codes to link to the role.
         *
         * @return Role
         */
        public function createRole($name, $code = null, $is_super_user = false, $permission_codes = null)
        {
            $code = $this->sanitizeRoleCode( $code, $name );

            $role = Role::create( compact( 'code', 'name', 'is_super_user' ) );

            if ( is_array( $permission_codes ) && count( $permission_codes ) )
            {
                $role->permissions()->sync( Permission::whereIn( 'code', $permission_codes )->lists( 'id' ) );
            }

            $this->events->fire( new RoleWasCreated( $role ) );

            return $role;
        }

        /**
         * Creates a permissions, optionally linking it to a role
         *
         * @param      $code
         * @param      $name
         * @param null $attach_to_role
         *
         * @return static
         */
        public function createPermission($code, $name, $attach_to_role = null)
        {
            $permission = Permission::create( compact( 'code', 'name' ) );

            if ( is_a( $attach_to_role, 'Dencker\Watchtower\Models\Role' ) )
            {
                /** @var Role $attach_to_role */
                $attach_to_role->permissions()->save( $permission );
            }

            $this->events->fire( new PermissionWasCreated( $permission ) );

            return $permission;
        }

        /**
         * Attaches a role to an actor (fx. a user)
         *
         * @param Model $actor
         * @param Role  $role
         *
         * @return bool
         */
        public function attachRoleToActor(Model $actor, Role $role)
        {
            WatchtowerActor::firstOrCreate( [
                'role_id'    => $role->getKey(),
                'actor_id'   => $actor->getKey(),
                'actor_type' => $actor->getMorphClass(),
            ] );

            $this->events->fire( new RolesWereAttachedToActor( $actor, collect( [$role] ) ) );

            return true;
        }

        /**
         * Attaches a collection or array of roles to an actor
         *
         * @param Model                                $actor
         *
         * @param array|\Illuminate\Support\Collection $roles
         *
         * @return bool
         *
         */
        public function attachRolesToActor(Model $actor, $roles)
        {

            foreach ($roles as $role)
            {
                WatchtowerActor::firstOrCreate( [
                    'role_id'    => $role->getKey(),
                    'actor_id'   => $actor->getKey(),
                    'actor_type' => $actor->getMorphClass(),
                ] );
            }

            $this->events->fire( new RolesWereAttachedToActor( $actor, $roles ) );

            return true;
        }


        /**
         * Attaches a permission to a Role
         *
         * @param Permission $permission
         * @param Role       $role
         *
         * @return Model
         */
        public function addPermissionToRole(Permission $permission, Role $role)
        {
            $role->permissions()->save( $permission );

            $this->events->fire( new PermissionWasAttachedToRole( $permission, $role ) );

            return true;
        }

        /**
         * Detaches a permission from a role.
         *
         *
         * @param Permission $permission
         * @param Role       $role
         *
         * @return int
         */
        public function removePermissionFromRole(Permission $permission, Role $role)
        {
            $role->permissions()->detach( $permission->id );

            $this->events->fire( new PermissionWasRemovedFromRole( $permission, $role ) );

            return true;
        }

        /**
         * Returns all available roles, optionally eager loading any relationships
         *
         * @param null|string|array $with Relations to eager-load
         *
         * @return \Illuminate\Database\Eloquent\Collection|static[]
         */
        public function allRoles($with = null)
        {
            if ( !is_null( $with ) )
                return Role::with( $with )->get();

            return Role::all();
        }


        /**
         * Returns all available permissions
         *
         * @param null|string|array $with Relations to eager-load
         *
         * @return \Illuminate\Database\Eloquent\Collection|static[]
         */
        public function allPermissions($with = null)
        {
            if ( !is_null( $with ) )
                return Permission::with( $with )->get();

            return Permission::all();
        }

        /**
         * Sanitizes a role code, using the name to generate one if it isn't provided.
         *
         * @param string|null $code
         * @param string      $name
         *
         * @return string
         */

        private function sanitizeRoleCode($code, $name = "name")
        {
            if ( is_null( $code ) )
                $code = Str::snake( Str::camel( $name ) );

            return $code;
        }
    }
