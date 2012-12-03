<?php
namespace NGS\ModelBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use NGS\Client\Exception\NotFoundException;
use NGS\Client\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use NGS\ModelBundle\Security\User;


class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        try {
            $user = \Security\User::find($username);
        }
        catch(UnauthorizedException $ex) {
            $this->getContainer()->get('messenger');
            $this->get('messenger')->error('Invalid username or password');
            return new User($username, '', array());
        }
        catch(NotFoundException $e) {
            $this->getContainer('messenger')->error('Invalid username or password');
            return false;
        }

        if($user) {
            // @todo handle user roles outside
            $roles = array('ROLE_ADMIN');
            return new User($user->Name, $user->Password, $roles, $user);
        }
        else {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'NGS\ModelBundle\Security\User';
    }
}