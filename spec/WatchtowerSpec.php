<?php

    namespace spec\Dencker\Watchtower;

    use Dencker\Watchtower\Config;
    use Dencker\Watchtower\Session;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Laracasts\TestDummy\Factory;
    use Prophecy\Argument;
    use Prophecy\Prophet;
    use spec\stubs\Actor;
    use spec\stubs\ActorWithoutContract;

    class WatchtowerSpec extends LaravelSpec
    {
        protected $actor;
        protected $session;
        protected $roles_collection;

        /**
         * @param \spec\stubs\Actor       $actor
         * @param \Dencker\Watchtower\Session $session
         */
        public function let(Actor $actor, Session $session)
        {
            $config = (new Prophet)->prophesize('\Dencker\Watchtower\Config');
            $config->get('primary_actor')->willReturn('spec\stubs\Actor');

            Config::setInstance($config->reveal());

            $this->actor   = $actor;
            $this->session = $session;

            $this->beConstructedWith( $actor, $session );
        }

        function it_is_initializable()
        {
            $this->shouldHaveType( 'Dencker\Watchtower\Watchtower' );
        }

        function it_throws_an_exception_if_the_actor_does_not_implement_watchtower_contract(ActorWithoutContract $actor)
        {
            $this->shouldThrow()->during( '__construct' );
            $this->beConstructedWith( $actor );
        }

        function it_retrieves_roles_belonging_to_a_user(MorphToMany $morphToMany)
        {
            $this->actor->morphToMany( 'Dencker\Watchtower\Models\Role', 'actor', 'watchtower_actors' )->shouldBeCalled();

            $this->actorHasRoles( $morphToMany );

            $this->roles->shouldReturn( $this->roleCollection() );

        }

        function it_saves_roles_to_the_session_after_the_first_database_call(MorphToMany $morphToMany)
        {
            $this->session->getRoles()->shouldBeCalled()->willReturn( null );

            $this->actor->morphToMany( 'Dencker\Watchtower\Models\Role', 'actor', 'watchtower_actors' )->shouldBeCalled();

            $this->session->setRoles($this->roleCollection() )->shouldBeCalled();


            $this->actorHasRoles( $morphToMany );

            $this->roles->shouldReturn( $this->roleCollection() );
        }

        function it_skips_database_calls_when_roles_are_fetched_if_the_session_is_set()
        {
            $this->rolesShouldBeFetchedFromSession();

            $this->roles->shouldReturn( $this->roleCollection() );
        }


        function it_retrieves_permissions_through_the_attached_roles(MorphToMany $morphToMany)
        {
            $roles = $this->roleCollectionWithPermissions();

            $this->actorHasRoles( $morphToMany, $roles );

            $this->permissions->lists( 'code' )->shouldReturn( ['permission_1', 'permission_2', 'permission_3', 'permission_4', 'permission_5'] );
        }

        function it_retrieves_permissions_from_the_session(MorphToMany $morphToMany)
        {
            $this->session->getPermissions()->willReturn( collect( [
                Factory::build( 'permission', ['code' => 'permission_1'] ),
                Factory::build( 'permission', ['code' => 'permission_2'] ),
                Factory::build( 'permission', ['code' => 'permission_3'] )
            ] ) )->shouldBeCalled();

            $this->actorRoleRelationShouldNotBeCalled();

            $this->actorHasRoles( $morphToMany );


            $this->permissions->lists( 'code' )->shouldReturn( ['permission_1', 'permission_2', 'permission_3'] );

        }

        function it_checks_whether_a_user_has_a_permission(MorphToMany $morphToMany)
        {
            $this->actorHasRoles( $morphToMany, $this->roleCollectionWithPermissions() );

            $this->hasPermission( 'permission_1' )->shouldReturn( true );
            $this->hasPermission( 'not_exisiting' )->shouldReturn( false );
        }

        function it_checks_whether_a_user_is_a_super_user_and_grants_all_permissions(MorphToMany $morphToMany)
        {
            $this->actorHasRoles( $morphToMany, new Collection( [$this->roleWithPermission()] ) );
            $this->isSuperUser()->shouldReturn( false );
            $this->hasPermission( 'any_permission_in_the_world_existing_or_not' )->shouldReturn( false );

            $this->actorHasRoles( $morphToMany, new Collection( [Factory::build('superuser_role'),Factory::build('role')] ) );
            $this->isSuperUser()->shouldReturn( true );
            $this->hasPermission( 'any_permission_in_the_world_existing_or_not' )->shouldReturn( true );

        }

        /**
         * @param MorphToMany $morphToMany
         *
         * @return Collection
         * @internal param int $roles_count
         *
         */
        private function actorHasRoles(MorphToMany $morphToMany, $role_collection = null)
        {
            $roleCollection = $role_collection ?: $this->roleCollection();

            $morphToMany->get()->willReturn( $roleCollection );

            $this->actor->morphToMany( 'Dencker\Watchtower\Models\Role', 'actor', 'watchtower_actors' )->willReturn( $morphToMany );

        }

        /**
         * @return Collection
         */
        private function roleCollection()
        {
            return $this->roles_collection ?: $this->roles_collection = new Collection( [
                Factory::build( 'role' ),
                Factory::build( 'role' ),
                Factory::build( 'role' ),
                Factory::build( 'role' ),
                Factory::build( 'role' ),
            ] );
        }

        private function roleWithPermission($permission_attributes = [], $superuser = false)
        {

            $prophecy = ( new Prophet )->prophesize( 'Dencker\Watchtower\Models\Role' );

//            $prophecy->getAttribute( 'is_super_user' )->willReturn( $superuser );

            $prophecy->getAttribute( "permissions" )->willReturn( new Collection( [Factory::build( 'permission', $permission_attributes )] ) );

            return $prophecy;
        }

        private function rolesShouldBeFetchedFromSession($role_collection = null)
        {
            $this->session->getRoles()->shouldBeCalled()->willReturn( $role_collection ?: $this->roleCollection() );
            $this->actorRoleRelationShouldNotBeCalled();
        }

        /**
         * @return Collection
         */
        private function roleCollectionWithPermissions()
        {
            $roles = new Collection( [
                $this->roleWithPermission( ['code' => 'permission_1'] ),
                $this->roleWithPermission( ['code' => 'permission_2'] ),
                $this->roleWithPermission( ['code' => 'permission_3'] ),
                $this->roleWithPermission( ['code' => 'permission_4'] ),
                $this->roleWithPermission( ['code' => 'permission_5'] ),
            ] );
            return $roles;
        }

        /**
         * @return mixed
         */
        private function actorRoleRelationShouldNotBeCalled()
        {
            return $this->actor->morphToMany( 'Dencker\Watchtower\Models\Role', 'actor', 'watchtower_actors' )->shouldNotBeCalled();
        }
    }
