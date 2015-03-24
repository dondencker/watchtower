<?php

    namespace spec\Dencker\Watchtower;

    use Illuminate\Session\SessionInterface as SessionContract;
    use Illuminate\Session\SessionManager;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class SessionSpec extends ObjectBehavior
    {
        protected $session;

        function let(SessionManager $session, SessionContract $session_contract)
        {
            $this->session = $session_contract;
            
            $session->driver()->willReturn($session_contract);
            $this->beConstructedWith( $session );
        }

        function it_is_initializable()
        {
            $this->shouldHaveType( 'Dencker\Watchtower\Session' );
        }

        function it_returns_null_if_no_session_is_set()
        {
            $this->getRoles()->shouldReturn( null );
        }

        function it_saves_permissions_to_the_session()
        {
            $this->session->set( 'watchtower.permissions', 'permissions' )->shouldBeCalled();
            $this->session->save()->shouldBeCalled();


            $this->setPermissions( 'permissions' );
        }


        function it_saves_roles_to_the_session()
        {
            $this->session->set( 'watchtower.roles', 'roles' )->shouldBeCalled();
            $this->session->save()->shouldBeCalled();

            $this->setRoles( 'roles' );
        }

        function it_returns_roles_from_the_session()
        {
            $this->session->get( 'watchtower.roles' )->shouldBeCalled()->willReturn( 'Mocked_roles' );

            $this->getRoles()->shouldReturn( 'Mocked_roles' );
        }

        function it_returns_permissions_from_the_session()
        {
            $this->session->get( 'watchtower.permissions' )->shouldBeCalled()->willReturn( 'mocked_permissions' );

            $this->getPermissions()->shouldReturn( 'mocked_permissions' );
        }

        function it_clears_related_sessions()
        {
            $this->session->remove( 'watchtower.roles' )->shouldBeCalled();
            $this->session->remove( 'watchtower.permissions' )->shouldBeCalled();

            $this->clear();
        }

    }
