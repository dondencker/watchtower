<?php
    namespace Dencker\Watchtower;


    use DB;
    use Dencker\Watchtower\Models\Permission;
    use Dencker\Watchtower\Models\Role;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Str;

    class WatchtowerRepository
    {

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

//            dd($name, $code,$role);

            if ( is_array( $permission_codes ) && count( $permission_codes ) )
            {
                $role->permissions()->sync( Permission::whereIn( 'code', $permission_codes )->lists( 'id' ) );
            }

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
            return DB::table( 'watchtower_actors' )->insert( [
                'role_id'    => $role->getKey(),
                'actor_id'   => $actor->getKey(),
                'actor_type' => $actor->getMorphClass(),
            ] );
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
            return $role->permissions()->save( $permission );
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
            return $role->permissions()->detach( $permission->id );
        }

        /**
         * Returns all available roles
         *
         * @return \Illuminate\Database\Eloquent\Collection|static[]
         */
        public function allRoles()
        {
            return Role::all();
        }


        /**
         * Returns all available permissions
         *
         * @return \Illuminate\Database\Eloquent\Collection|static[]
         */
        public function allPermissions()
        {
            return Permission::all();
        }

        private function sanitizeRoleCode($code, $name = "name")
        {
            if ( is_null( $code ) )
                $code = Str::snake( Str::camel( $name ) );

            return $code;
        }
    }
